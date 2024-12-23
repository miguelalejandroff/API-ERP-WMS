<?php

namespace App\ERP\Adapters\OrdenEntrada;

use App\ERP\Contracts\OrdenEntradaService;
use App\Http\Controllers\DeleteDespachoEnTransito;
use App\Http\Controllers\RespuestaDespachoController;
use App\ERP\Handler\SaldoBodegaHandler2;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Exception;

class DespachoTransito implements OrdenEntradaService
{
    protected $context;
    protected $deleteDespachoController;
    protected $respuestaDespachoController;
    protected $saldoBodegaHandler;

    /**
     * Constructor con inyección de dependencias.
     */
    public function __construct(
        $context,
        DeleteDespachoEnTransito $deleteDespachoController,
        RespuestaDespachoController $respuestaDespachoController,
        SaldoBodegaHandler2 $saldoBodegaHandler
    ) {
        $this->context = $context;
        $this->deleteDespachoController = $deleteDespachoController;
        $this->respuestaDespachoController = $respuestaDespachoController;
        $this->saldoBodegaHandler = $saldoBodegaHandler;
    }

    /**
     * Ejecuta el proceso principal de despacho en tránsito.
     */
    public function run(): JsonResponse
    {
        DB::beginTransaction();

        try {
            $guiaRecepcion = $this->context->guiaRecepcion;

            // Validar guía de recepción
            $this->validateGuiaRecepcion($guiaRecepcion);

            // Eliminar registros
            $this->deleteDespachoController->eliminarRegistro($this->buildDeleteRequest($guiaRecepcion));

            // Procesar detalles de despacho
            $this->procesarDetallesDespacho($guiaRecepcion);

            // Actualizar saldo de bodega
            $this->actualizarSaldoBodega($this->context);

            DB::commit();

            return $this->successResponse("Proceso de despacho en tránsito completado sin problemas.");
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Error en DespachoTransito', [
                'message' => $e->getMessage(),
                'context' => $this->context
            ]);

            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Valida los datos de la guía de recepción.
     */
    private function validateGuiaRecepcion($guiaRecepcion): void
    {
        if (empty($guiaRecepcion->numeroDocumento) || empty($guiaRecepcion->fechaRecepcionWMS)) {
            throw new Exception("Datos de guía de recepción incompletos: numeroDocumento o fechaRecepcionWMS faltantes.");
        }
    }

    /**
     * Procesa los detalles de despacho.
     */
    private function procesarDetallesDespacho($guiaRecepcion): void
    {
        foreach ($guiaRecepcion->documentoDetalle as $detalle) {
            $this->respuestaDespachoController
                ->procesarRespuesta($this->buildRespuestaRequest($guiaRecepcion, $detalle));
        }
    }

    /**
     * Actualiza el saldo de bodega usando el handler.
     */
    private function actualizarSaldoBodega($context): void
    {
        try {
            $this->saldoBodegaHandler->handle($context);
            Log::info('Saldo de bodega actualizado correctamente.');
        } catch (Exception $e) {
            Log::error('Error al actualizar saldo de bodega: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Construye el request para eliminar despacho.
     */
    private function buildDeleteRequest($guiaRecepcion): Request
    {
        return new Request([
            'numeroDocumento' => $guiaRecepcion->numeroDocumento,
            'fechaRecepcionWMS' => $guiaRecepcion->fechaRecepcionWMS,
        ]);
    }

    /**
     * Construye el request para procesar respuesta de despacho.
     */
    private function buildRespuestaRequest($guiaRecepcion, $detalle): Request
    {
        return new Request([
            'numeroDocumento' => $guiaRecepcion->numeroDocumento,
            'fechaRecepcionWMS' => $guiaRecepcion->fechaRecepcionWMS,
            'tipoDocumentoERP' => $guiaRecepcion->tipoDocumentoERP,
            'documentoDetalle' => $detalle,
        ]);
    }

    /**
     * Retorna una respuesta JSON de éxito.
     */
    private function successResponse(string $message): JsonResponse
    {
        return response()->json([
            "success" => true,
            "message" => $message
        ], 200);
    }

    /**
     * Retorna una respuesta JSON de error.
     */
    private function errorResponse(string $message): JsonResponse
    {
        return response()->json([
            "success" => false,
            "message" => $message
        ], 500);
    }
}
