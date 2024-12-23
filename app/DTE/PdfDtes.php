<?php

namespace App\DTE;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use SimpleXMLElement;

class PdfDtes
{
  protected $rutEmisor = '81643200-6';

  public static function factura($nroDocto, $fechaEmision, $montoTotal, $copiaCedible = 0)
  {
    return static::send('81643200-6', 33, $nroDocto, $fechaEmision, $montoTotal, $copiaCedible);
  }

  public static function boleta($nroDocto, $fechaEmision, $montoTotal, $copiaCedible = 0)
  {
    return static::send('81643200-6', 39, $nroDocto, $fechaEmision, $montoTotal, $copiaCedible);
  }

  public static function notaCredito($nroDocto, $fechaEmision, $montoTotal, $copiaCedible = 0)
  {
    return static::send('81643200-6', 61, $nroDocto, $fechaEmision, $montoTotal, $copiaCedible);
  }

  public static function notaDebito($nroDocto, $fechaEmision, $montoTotal, $copiaCedible = 0)
  {
    return static::send('81643200-6', 56, $nroDocto, $fechaEmision, $montoTotal, $copiaCedible);
  }

  public static function guia($nroDocto, $fechaEmision, $montoTotal, $copiaCedible = 0)
  {
    return static::send('81643200-6', 52, $nroDocto, $fechaEmision, $montoTotal, $copiaCedible);
  }

  public static function send($rutEmisor, $tipoDocumento, $nroDocto, $fechaEmision, $montoTotal, $copiaCedible = 0)
  {
    try {
      // Configura la solicitud SOAP
      $requestBody = static::buildSoapRequest($rutEmisor, $tipoDocumento, $nroDocto, $fechaEmision, $montoTotal, $copiaCedible);

      // Realiza la solicitud al servicio web
      $response = Http::withHeaders([
        'Content-Type' => 'text/xml; charset=utf-8',
        'SOAPAction' => 'http://tempuri.org/PDF_DTEs',
      ])->send('POST', 'http://198.1.1.156/wsFactLocal/DTELocal.asmx', [
        'body' => $requestBody,
      ]);

      if (!$response->successful()) {
        throw new Exception("Error en la solicitud SOAP: {$response->body()}");
      }

      // Procesa la respuesta SOAP
      $decodedData = static::decodeResponse($response->body());

      // Guarda el PDF en el almacenamiento
      $filePath = static::savePdf($decodedData, $nroDocto);

      return $filePath;
    } catch (Exception $e) {
      Log::error("Error al generar el PDF para el documento {$nroDocto}: " . $e->getMessage());
      throw new Exception("No se pudo generar el PDF. Verifica los logs para más detalles.");
    }
  }

  protected static function buildSoapRequest($rutEmisor, $tipoDocumento, $nroDocto, $fechaEmision, $montoTotal, $copiaCedible)
  {
    return <<<EOT
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <PDF_DTEs xmlns="http://tempuri.org/">
      <Rut_Emisor>{$rutEmisor}</Rut_Emisor>
      <Tipo_Documento>{$tipoDocumento}</Tipo_Documento>
      <Nro_Docto>{$nroDocto}</Nro_Docto>
      <Fecha_Emision>{$fechaEmision}</Fecha_Emision>
      <Monto_Total>{$montoTotal}</Monto_Total>
      <CopiaCedible>{$copiaCedible}</CopiaCedible>
    </PDF_DTEs>
  </soap:Body>
</soap:Envelope>
EOT;
  }

  protected static function decodeResponse($responseBody)
  {
    try {
      $xmlData = new SimpleXMLElement($responseBody);
      $namespace = $xmlData->getNamespaces(true);

      // Obtener la respuesta dentro del Body
      $result = $xmlData->children($namespace['soap'])
        ->Body
        ->PDF_DTEsResponse
        ->PDF_DTEsResult;

      // Decodificar el contenido en base64
      return base64_decode($result);
    } catch (Exception $e) {
      throw new Exception("Error al procesar la respuesta SOAP: " . $e->getMessage());
    }
  }

  protected static function savePdf($decodedData, $nroDocto)
  {
    $filePath = "pdf/pdf_{$nroDocto}.pdf";

    if (Storage::disk('local')->put($filePath, $decodedData)) {
      return $filePath;
    } else {
      throw new Exception("No se pudo guardar el PDF en la ruta {$filePath}");
    }
  }
}
