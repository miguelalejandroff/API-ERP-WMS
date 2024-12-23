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

/**
 * Clase GuiaRecepcionHandler
 * 
 * Esta clase se encarga de manejar la creación de guías de recepcion basándose en la información de la recepción de mercancía.
 * Hereda de la clase Handler.
 */
class GuiaRecepcionHandler extends Handler
{

    /**
     * @var string $bodegaOrigen define el ID de la bodega de origen. Por defecto es 29.
     */
    private $bodegaOrigen = 29;


    /**
     * @var string $sucursalOrigen define el ID de la sucursal de origen. Por defecto es 1.
     */
    private $sucursalOrigen = 1;

    /**
     * @var string $sucursalDestino define el ID de la sucursal de destino. Por defecto es null.
     */
    private $sucursalDestino = null;

    /**
     * @var string $MaestroProductoHandler actualiza el cmproductos
     */
    private $saldoBodegaHandler;

    //public function __construct(SaldoBodegaHandler $saldoBodegaHandler = null)
    //{
    //    $this->saldoBodegaHandler = $saldoBodegaHandler;
    //}

    public function handle(OrdenEntradaContext $context)
    {

        Log::info('GuiaRecepcionHandler', ['message' => 'Inicia Proceso']);

        Log::info('Antes de cargar el documento en GuiaRecepcionHandler');
        $context->solicitudRecepcion->cargarDocumento($context->recepcionWms->getDocumento('numeroDocumento'));

            $context->guiaRecepcion->guardarDocumento(function ($documento) use ($context) {

                $documento->gui_numero = $context->solicitudRecepcion->getDocumento('gui_numero');
                $documento->gui_tipgui = $context->solicitudRecepcion->tipoDocumento;
                $documento->gui_fechag = $context->recepcionWms->getDocumento('fechaRecepcionWMS')->format('Y-m-d');
                $documento->gui_ordcom = $context->solicitudRecepcion->getDocumento('gui_ordcom');

                $documento->gui_numrut = $context->solicitudRecepcion->getDocumento('gui_numrut');
                $documento->gui_digrut = $context->solicitudRecepcion->getDocumento('gui_digrut');
                $documento->gui_subcta = $context->solicitudRecepcion->getDocumento('gui_subcta');
                $documento->gui_nombre = $context->solicitudRecepcion->getDocumento('gui_nombre');

                $documento->gui_guipro = $context->solicitudRecepcion->getDocumento('gui_guipro') ?? 0;
                $documento->gui_facpro = $context->solicitudRecepcion->getDocumento('gui_facpro') ?? 0;
                $documento->gui_facals = $context->solicitudRecepcion->getDocumento('gui_facals') ?? 0;

                $documento->gui_sucori = $this->sucursalOrigen;
                $documento->gui_sucdes = $context->solicitudRecepcion->getDocumento('gui_sucdes');

                $documento->gui_paract = $context->parametro;
                $documento->gui_fecmod = $context->recepcionWms->getDocumento('fechaRecepcionWMS')->format('Y-m-d');
                $documento->gui_codusu = $context->solicitudRecepcion->getDocumento('gui_codusu');
                $documento->gui_empres = $context->empresa;
            });


            $context->recepcionWms->iterarDetalle(function ($detalle) use ($context) {

                $context->guiaRecepcion->guardarDetalle(function ($detalleRecepcion) use ($context, $detalle) {

                    $detalleSolicitud = $context->solicitudRecepcion->getDetalle('gui_produc', $detalle['codigoProducto']);

                    $detallePrecio = $context->guiaCompra->getDetalle('gui_produc', $detalle['codigoProducto']);


                    Log::info('Asignando gui_numero', ['valor' => $context->recepcionWms->getDocumento('numeroDocumento')]);
                    $detalleRecepcion->gui_numero = $context->recepcionWms->getDocumento('numeroDocumento');

                    $detalleRecepcion->gui_tipgui = $context->guiaRecepcion->tipoDocumento;

                    Log::info('Asignando gui_bodori', ['valor' => $this->bodegaOrigen]);
                    $detalleRecepcion->gui_bodori = $this->bodegaOrigen;

                    Log::info('Asignando gui_boddes', ['valor' => $detalleRecepcion->gui_boddes = $detalleSolicitud['gui_boddes']]);
                    $detalleRecepcion->gui_boddes = $detalleSolicitud['gui_boddes'];


                    Log::info('Asignando gui_produc', ['valor' => $detalle['codigoProducto']]);
                    $detalleRecepcion->gui_produc = $detalle['codigoProducto'];

                    Log::info('Asignando gui_descri', ['valor' => $detalleSolicitud['gui_descri']]);
                    $detalleRecepcion->gui_descri = $detalleSolicitud['gui_descri'];

                    Log::info('Asignando gui_canord', ['valor' => $detalle['cantidadSolicitada']]);
                    $detalleRecepcion->gui_canord = $detalle['cantidadSolicitada'];

                    Log::info('Asignando gui_canrep', ['valor' => $detalle['cantidadRecepcionada']]);
                    $detalleRecepcion->gui_canrep = $detalle['cantidadRecepcionada'];


                    $detalleRecepcion->gui_preuni =  $detallePrecio['gui_preuni'];
                });
            });

            $context->guiaRecepcion->cargarDocumento($context->guiaRecepcion->getDocumento('gui_numero'));


       
            
            

            $context->guiaRecepcion->enviaWms = true;

            Log::info('GuiaRecepcionHandler', ['message' => 'Se crea Documento para Guia de Recepcion', 'guiaRecepcion' => $context->guiaRecepcion->getDocumento()]);
        
        Log::info('GuiaRecepcionHandler', ['message' => 'Finaliza Proceso']);


    }

}
