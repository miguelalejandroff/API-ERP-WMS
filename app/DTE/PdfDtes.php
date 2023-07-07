<?php

namespace App\DTE;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use SimpleXMLElement;

class PdfDtes
{
  protected $rutEmisor = '81643200-6';

  public static function factura()
  {
    //static::send($this->rutEmisor, 33, $nroDocto, $fechaEmision, $montoTotal);
  }
  public static function boleta()
  {
  }
  public static function notaCredito()
  {
  }
  public static function notaDebito()
  {
  }
  public static function guia()
  {
  }
  public static function send($rutEmisor, $tipoDocumento, $nroDocto, $fechaEmision, $montoTotal, $copiaCedible = 0)
  {
    // Configura la solicitud SOAP con los datos proporcionados
    $requestBody = '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
          <soap:Body>
            <PDF_DTEs xmlns="http://tempuri.org/">
              <Rut_Emisor>' . $rutEmisor . '</Rut_Emisor>
              <Tipo_Documento>' . $tipoDocumento . '</Tipo_Documento>
              <Nro_Docto>' . $nroDocto . '</Nro_Docto>
              <Fecha_Emision>' . $fechaEmision . '</Fecha_Emision>
              <Monto_Total>' . $montoTotal . '</Monto_Total>
              <CopiaCedible>' . $copiaCedible . '</CopiaCedible>
            </PDF_DTEs>
          </soap:Body>
        </soap:Envelope>';

    // Realiza la solicitud SOAP al servicio web utilizando la clase Http de Laravel
    $response = Http::withHeaders([
      'Content-Type' => 'text/xml; charset=utf-8',
      'SOAPAction' => 'http://tempuri.org/PDF_DTEs'
    ])
      ->send('POST', 'http://198.1.1.156/wsFactLocal/DTELocal.asmx', [
        'body' => $requestBody
      ]);

    // Obtén el contenido de la respuesta
    $responseBody =  $response->body();
    $xml_data = new SimpleXMLElement($responseBody);

    // Decodifica el contenido binario en base64
    $base64Data = $xml_data->xpath('//soap:Body')[0]->PDF_DTEsResponse->PDF_DTEsResult;
    $decodedData = base64_decode($base64Data);

    // Guarda el contenido decodificado en un archivo
    $filePath = "/pdf/pdf_{$nroDocto}.pdf"; // Cambia la ruta y el nombre del archivo según tus necesidades

    // Utiliza el disco de almacenamiento que desees, por ejemplo 'local' o 'public'
    Storage::disk('local')->put($filePath, $decodedData);

    // Retorna la ruta completa del archivo descargado

    return Storage::download($filePath);
  }
}
