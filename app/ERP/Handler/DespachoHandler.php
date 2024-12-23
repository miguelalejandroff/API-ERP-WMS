<?php

namespace App\ERP\Handler;

use App\ERP\Build\Handler;
use App\Exceptions\CustomException;
use App\Enums\SaldoBodegaEnum;
use App\ERP\Context\OrdenEntradaContext;
use App\ERP\Contracts\OrdenEntradaService;
use App\Models\cmbodega;
use App\Models\cmdetgui;
use App\Models\cmguias;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use App\ERP\Handler\SaldoBodegaHandler;
use App\WMS\EndpointWMS;

class DespachoHandler {
    
    /**
     * @var string $bodegaOrigen define el ID de la bodega de origen. Por defecto es 29.
     */
    private $estado ="A";


    /**
     * @var string $sucursalOrigen define el ID de la sucursal de origen. Por defecto es 1.
     */
    private $descripcion = "Aut. para Guia Despacho";


    public function handle(OrdenEntradaContext $context)
    {

        Log::info('DespachoHandler', ['message' => 'Inicia Proceso']);

        $context->solicitudDespacho->cargarDocumento($context->recepcionWms->getDocumento('numeroDocumento'));

        if (!$context->solicitudDespacho->getDocumento()) {

            Log::info('DespachoHandler', ['message' => 'No existe Guia de Recepcion', 'guiaRecepcion' => $context->guiaRecepcion->getDocumento()]);

            $context->solicitudDespacho->guardarDocumento(function ($documento) use ($context) {

                $documento->des_folio = $context->solicitudDespacho->getDocumento('des_folio');
                $documento->des_tipo = $context->solicitudDespacho->getDocumento('des_tipo');
                $documento->des_marcaitem = $context->solicitudDespacho->getDocumento('des_marcaitem');
                $documento->des_fecha = $context->solicitudDespacho->getDocumento('des_fecha');

                $documento->des_numrut = $context->solicitudDespacho->getDocumento('des_numrut');
                $documento->des_digrut = $context->solicitudDespacho->getDocumento('des_digrut');
                $documento->des_subcta = $context->solicitudDespacho->getDocumento('des_subcta');
                $documento->des_nombre = $context->solicitudDespacho->getDocumento('des_nombre');

                $documento->des_guipro = $context->solicitudDespacho->getDocumento('des_guipro') ?? 0;
                $documento->des_facpro = $context->solicitudDespacho->getDocumento('des_facpro') ?? 0;
                $documento->des_facals = $context->solicitudDespacho->getDocumento('des_facals') ?? 0;

                $documento->des_sucori = $context->solicitudDespacho->getDocumento('des_facals');
                $documento->des_sucdes = $context->solicitudDespacho->getDocumento('gui_sucdes');

                $documento->des_estado = $this->estado;
                $documento->des_desestado = $this->descripcion;
                $documento->des_numgui = $context->solicitudDespacho->getDocumento('des_numgui');
                $documento->des_usuario = $context->solicitudDespacho->getDocumento('des_usuario');
                $documento->des_current = $context->solicitudDespacho->getDocumento('des_current');

            });

            $context->recepcionWms->iterarDetalle(function ($detalle) use ($context) {

                $context->solicitudDespacho->guardarDetalle(function ($detalleRecepcion) use ($context, $detalle) {

                    $detalleSolicitud = $context->solicitudRecepcion->getDetalle('des_codigo', $detalle['codigoProducto']);

                    $detalleRecepcion->des_folio = $context->solicitudDespacho->getDocumento('des_folio');
                    $detalleRecepcion->des_tipo = $context->guiaRecepcion->tipoDocumento;
                    $detalleRecepcion->des_fecha = $detalleSolicitud->des_fecha;

                    $detalleRecepcion->des_bodori = $detalleSolicitud->des_bodori;
                    $detalleRecepcion->des_boddes = $detalleSolicitud->des_boddes;

                    $detalleRecepcion->des_codigo = $detalleSolicitud->des_codigo;
                    $detalleRecepcion->des_newcod = $detalleSolicitud->des_newcod;

                    $detalleRecepcion->des_descri = $detalleSolicitud->des_descri;
                    $detalleRecepcion->des_unimed = $detalleSolicitud->des_unimed;

                    $detalleRecepcion->des_stockp = $detalle['cantidadRecepcionada'];
                    $detalleRecepcion->des_preuni = $detalleSolicitud->des_preuni;
                    $detalleRecepcion->des_msgsuc = $detalleSolicitud->des_msgsuc;
                    $detalleRecepcion->des_msggte = $detalleSolicitud->des_msggte;
                    $detalleRecepcion->des_estado = $this->estado;
                    $detalleRecepcion->des_fecaut = $detalleSolicitud->des_fecaut;
                    $detalleRecepcion->des_numgui = $detalleSolicitud->des_numgui;
                    $detalleRecepcion->des_usuaut = $detalleSolicitud->des_usuaut;
                });
            });


            $context->guiaRecepcion->cargarDocumento($context->guiaRecepcion->getDocumento('gui_numero'));

            $context->guiaRecepcion->enviaWms = true;

            Log::info('GuiaRecepcionHandler', ['message' => 'Se crea Documento para Guia de Recepcion', 'guiaRecepcion' => $context->guiaRecepcion->getDocumento()]);
        }
        Log::info('GuiaRecepcionHandler', ['message' => 'Finaliza Proceso']);

    }
}