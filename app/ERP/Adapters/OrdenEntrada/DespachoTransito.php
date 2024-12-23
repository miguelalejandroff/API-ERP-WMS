<?php

namespace App\ERP\Adapters\OrdenEntrada;

use App\ERP\Contracts\OrdenEntradaService;
use App\Http\Controllers\DeleteDespachoEnTransito;
use App\Http\Controllers\RespuestaDespachoController;
use App\ERP\Handler\SaldoBodegaHandler2;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class DespachoTransito implements OrdenEntradaService
{
    protected $context;

    public function __construct($context)
    {
        $this->context = $context;
    }

    public function run()
    {
        DB::beginTransaction();

        try {
            $guiaRecepcion = $this->context->guiaRecepcion;

            // Validación de datos obligatorios
            $this->validateGuiaRecepcion($guiaRecepcion);

            // Eliminar registros usando el controlador DeleteDespachoEnTransito
            $this->getDeleteDespachoEnTransitoController()
                ->eliminarRegistro($this->buildDeleteRequest($guiaRecepcion));

            // Procesar respuesta de despacho
            foreach ($guiaRecepcion->documentoDetalle as $detalle) {
                $this->getRespuestaDespachoController()
                    ->procesarRespuesta($this->buildRespuestaRequest($guiaRecepcion, $detalle));
            }

            // Actualizar saldos de bodega
            $this->saldoBodegaHandler2($this->context);

            DB::commit();

            return response()->json([
                "success" => true,
                "message" => "Proceso de despacho en tránsito completado sin problemas"
            ], 200);

        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Error en DespachoTransito: ' . $e->getMessage(), [
                'context' => $this->context
            ]);

            return response()->json([
                "success" => false,
                "message" => $e->getMessage()
            ], 500);
        }
    }

    private function validateGuiaRecepcion($guiaRecepcion)
    {
        if (empty($guiaRecepcion->numeroDocumento) || empty($guiaRecepcion->fechaRecepcionWMS)) {
            throw new Exception("Datos de guía de recepción incompletos: numeroDocumento o fechaRecepcionWMS faltantes.");
        }
    }

    public function getDeleteDespachoEnTransitoController()
    {
        return app(DeleteDespachoEnTransito::class);
    }

    public function getRespuestaDespachoController()
    {
        return app(RespuestaDespachoController::class);
    }

    private function saldoBodegaHandler2($context)
    {
        try {
            $handler = new SaldoBodegaHandler2();
            $handler->handle($context);
            Log::info('Saldo de bodega actualizado correctamente.');
        } catch (Exception $e) {
            Log::error('Error al actualizar saldo de bodega: ' . $e->getMessage());
            throw $e;
        }
    }

    protected function buildDeleteRequest($guiaRecepcion)
    {
        return new Request([
            'numeroDocumento' => $guiaRecepcion->numeroDocumento,
            'fechaRecepcionWMS' => $guiaRecepcion->fechaRecepcionWMS,
        ]);
    }

    protected function buildRespuestaRequest($guiaRecepcion, $detalle)
    {
        return new Request([
            'numeroDocumento' => $guiaRecepcion->numeroDocumento,
            'fechaRecepcionWMS' => $guiaRecepcion->fechaRecepcionWMS,
            'tipoDocumentoERP' => $guiaRecepcion->tipoDocumentoERP,
            'documentoDetalle' => $detalle,
        ]);
    }
}
