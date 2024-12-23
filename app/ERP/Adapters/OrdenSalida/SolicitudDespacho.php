<?php

namespace App\ERP\Adapters\OrdenSalida;

use App\ERP\Contracts\OrdenSalidaService;
use App\Http\Controllers\DespachoController;
use App\Http\Controllers\DespachoClienteController;
use App\Http\Controllers\PedidosController;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SolicitudDespacho implements OrdenSalidaService
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
            foreach ($this->context->recepcion->documentoDetalle as $detalle) {
                $tipoDocumentoERP = $this->context->recepcion->tipoDocumentoERP;
                    
                $controller = $this->getController($tipoDocumentoERP);
                $controller->actualizardesdeWMS($this->buildRequest($detalle));
            }
            DB::commit();

            return response()->json(["message" => "Proceso de Despacho sin Problemas"], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(["message" => $e->getMessage()], 500);
        }
    } 

    public function getController($tipoDocumentoERP)
    {
        if ($tipoDocumentoERP == 'P') {
            return app(PedidosController::class);
        } elseif ($tipoDocumentoERP == '16') {
            return app(DespachoClienteController::class);      
        } else {
            return app(DespachoController::class);
        }
        
    }

    protected function buildRequest($detalle)
    {
        return new Request([
            'numeroDocumento' => $this->context->recepcion->numeroDocumento,
            'numeroOrdenSalida' => $this->context->recepcion->numeroOrdenSalida,
            'tipoDocumentoWMS' => $this->context->recepcion->tipoDocumentoWMS,
            'tipoDocumentoERP' => $this->context->recepcion->tipoDocumentoERP,
            'usuario' => $this->context->recepcion->usuario,
            'codigoProducto' => $detalle['codigoProducto'],
            'cantidadRecepcionada' => $detalle['cantidadRecepcionada'],
        ]);
    }             
}
