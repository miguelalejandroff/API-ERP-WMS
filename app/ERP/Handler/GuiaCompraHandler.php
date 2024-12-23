<?php

namespace App\ERP\Handler;

use App\ERP\Build\Handler;
use App\ERP\Context\OrdenEntradaContext;
use App\Exceptions\CustomException;
use App\Models\guicompra;
use App\Models\guidetcompra;
use App\WMS\Contracts\Inbound\OrdenEntradaService;
use App\WMS\EndpointWMS;
use Exception;
use Illuminate\Support\Facades\Log;
use App\Models\cmdetord;
use App\Models\cmordcom;
use App\Models\cmproductos;
use App\ERP\Handler\MaestroProductoHandler;
use Illuminate\Support\Facades\Http;
use Psy\Readline\Hoa\Console;

/**
 * Clase GuiaCompraHandler
 * 
 * Esta clase se encarga de manejar la creación de guías de compra basándose en la información de la recepción de mercancía.
 * Hereda de la clase Handler.
 */
class GuiaCompraHandler extends Handler
{

    /**
     * @var string $bodegaOrigen define el ID de la bodega de origen. Por defecto es 0.
     */
    private $bodegaOrigen  = 0;

    /**
     * @var string $bodegaDestino define el ID de la bodega de destino. Por defecto es 29.
     */
    private $bodegaDestino  = 29;

    /**
     * @var string $sucursalOrigen define el ID de la sucursal de origen. Por defecto es 0.
     */
    private $sucursalOrigen = 0;

    /**
     * @var string $sucursalDestino define el ID de la sucursal de destino. Por defecto es 1.
     */
    private $sucursalDestino = 1;

    
    public function handle(OrdenEntradaContext $context)
    {
        Log::info('GuiaCompraHandler', ['message' => 'Inicia Proceso']);

        $context->guiaCompra->cargarDocumento($context->ordenCompra->getDocumento('ord_numcom'));
        $context->guiaCompra->enviaWms = true;

        if (!$context->guiaCompra->getDocumento()) {

            Log::info('GuiaCompraHandler', ['message' => 'No existe Guia de Compra', 'guiaCompra' => $context->guiaCompra->getDocumento()]);
            $context->guiaCompra->guardarDocumento(function ($documento) use ($context) {

                $documento->gui_numero = $context->ordenCompra->getDocumento('ord_numcom');
                $documento->gui_tipgui = $context->guiaCompra->tipoDocumento;
                $documento->gui_fechag = $context->recepcionWms->getDocumento('fechaRecepcionWMS')->format('Y-m-d');
                $documento->gui_ordcom = $context->ordenCompra->getDocumento('ord_numcom');

                $documento->gui_numrut = $context->proveedor->getDocumento('aux_numrut');
                $documento->gui_digrut = $context->proveedor->getDocumento('aux_digrut');
                $documento->gui_subcta = $context->proveedor->getDocumento('aux_claves');
                $documento->gui_nombre = $context->proveedor->getDocumento('aux_nombre');

                $documento->gui_guipro = $context->solicitudRecepcion->getDocumento('gui_guipro') ?? 0;
                $documento->gui_facpro = $context->solicitudRecepcion->getDocumento('gui_facpro') ?? 0;
                $documento->gui_facals = $context->solicitudRecepcion->getDocumento('gui_facals') ?? 0;

                $documento->gui_sucori = $this->sucursalOrigen;
                $documento->gui_sucdes = $this->sucursalDestino;

                $documento->gui_paract = $context->parametro;
                $documento->gui_fecmod = $context->recepcionWms->getDocumento('fechaRecepcionWMS')->format('Y-m-d');
                $documento->gui_codusu = $context->solicitudRecepcion->getDocumento('gui_codusu');
                $documento->gui_empres = $context->empresa;

                $documento->gui_current = $context->recepcionWms->getDocumento('fechaRecepcionWMS')->format('Y-m-d H:i');
            });

            $context->ordenCompra->iterarDetalle(function ($detalle) use ($context) {

                $context->guiaCompra->guardarDetalle(function ($detalleCompra) use ($context, $detalle) {

                    $detalleCompra->gui_clave = $context->guiaCompra->getDocumento('gui_clave');
                    $detalleCompra->gui_numero = $detalle->ord_numcom;
                    $detalleCompra->gui_tipgui = $context->guiaCompra->tipoDocumento;

                    $detalleCompra->gui_bodori = $this->bodegaOrigen;
                    $detalleCompra->gui_boddes = $this->bodegaDestino;

                    $detalleCompra->gui_produc = $detalle->ord_produc;
                    $detalleCompra->gui_descri = $detalle->ord_descri;
                    $detalleCompra->gui_unimed = $detalle->ord_unimed;

                    $detalleCompra->gui_canord = $detalle->calculaCosto->cantidadCalculada;
                    $detalleCompra->gui_canrep = $detalle->calculaCosto->cantidadCalculada;
                    $detalleCompra->gui_preuni = $detalle->calculaCosto->precioCalculado;
                    $detalleCompra->gui_saldo = $detalle->calculaCosto->cantidadCalculada;
                });
            });

            $context->guiaCompra->cargarDocumento($context->guiaCompra->getDocumento('gui_numero'));

            $context->guiaCompra->enviaWms = true;

            Log::info('GuiaCompraHandler', ['message' => 'Se crea Documento para Guia de Compra', 'guiaCompra' => $context->guiaCompra->getDocumento()]);

            $saldoBodegaTransitoHandler = new SaldoBodegaTransitoHandler();
            $saldoBodegaTransitoHandler->handle($context);

        }

        Log::info('GuiaCompraHandler', ['message' => 'Inicia Actualizacion de Saldo Guia de Compra', 'guiaCompra' => $context->guiaCompra->getDocumento()]);

        $context->guiaCompra->iterarDetalle(function ($detalle) use ($context) {


            $recepcionWms = $context->recepcionWms->getDetalle('codigoProducto', $detalle->gui_produc);

            if ($recepcionWms) {

                $criteriosBusqueda = [
                    'gui_numero' => $detalle->gui_numero,
                    'gui_tipgui' => $detalle->gui_tipgui,
                    'gui_produc' => $detalle->gui_produc
                ];

                $context->guiaCompra->guardarDetalle(
                    function ($detalleCompra) use ($recepcionWms) {
                        $detalleCompra->gui_saldo -= $recepcionWms['cantidadRecepcionada'];
                    },
                    $criteriosBusqueda
                );

            }
        });

        
        // Variables para almacenar los gui_produc y gui_canord asociados a los gui_produc que no están presentes en la recepción WMS
        $guiProducNoPresentes = [];
        $guiCanrepNoPresentes = [];

        // Iterar sobre los detalles de la solicitud de recepción
        $context->solicitudRecepcion->iterarDetalle(function ($detalleSolicitud) use ($context, &$guiProducNoPresentes, &$guiCanrepNoPresentes) {

            // Verificar si el gui_produc actual no está presente en la recepción WMS
            $recepcionWms = $context->recepcionWms->getDetalle('codigoProducto', $detalleSolicitud->gui_produc);
            if (!$recepcionWms) {
                // Si no está presente, almacenar gui_produc y gui_canord asociados a ese gui_produc en las variables respectivas
                $guiProducNoPresentes[] = $detalleSolicitud->gui_produc;
                $guiCanrepNoPresentes[] = $detalleSolicitud->gui_canrep;
            }
        });


        // Iterar sobre los detalles de la orden de compra
        $context->ordenCompra->iterarDetalle(function ($detalle) use ($context, &$guiProducNoPresentes, &$guiCanrepNoPresentes) {

            $recepcionordWms = $context->recepcionWms->getDetalle('codigoProducto', $detalle->ord_produc);

            $criteriosBusqueda = [
                'ord_numcom' => $detalle->ord_numcom,
    
                'ord_produc' => $detalle->ord_produc
            ];

            if ($recepcionordWms && $recepcionordWms['cantidadSolicitada'] !== $recepcionordWms['cantidadRecepcionada']) {

                $diferencia = $recepcionordWms['cantidadSolicitada'] - $recepcionordWms['cantidadRecepcionada'];

                $context->ordenCompra->guardarDetalle(
                    function ($detalleCompra) use ($diferencia) {
                        $detalleCompra->ord_saldos += $diferencia;
                    },
                    $criteriosBusqueda
                );
                $context->ordenCompra->guardarDocumento(
                    function ($documento){
                        $documento->ord_estado = 'P';
                    },
                    $criteriosBusqueda
                );
                        
                
            }
            if (in_array($detalle->ord_produc, $guiProducNoPresentes)) {
                // Obtener la cantidad correspondiente desde $guiCanrepNoPresentes asociado al gui_produc actual
                $cantidad = $guiCanrepNoPresentes[array_search($detalle->ord_produc, $guiProducNoPresentes)];
                
                $context->ordenCompra->guardarDetalle(
                    function ($detalleCompra) use ($cantidad) {
                        $detalleCompra->ord_saldos += $cantidad;
                    },
                    $criteriosBusqueda
                );
                $context->ordenCompra->guardarDocumento(
                    function ($documento){
                        $documento->ord_estado = 'P';
                    },
                    $criteriosBusqueda
                );
            }            
        });
        
        Log::info('GuiaCompraHandler', ['message' => 'Finaliza Actualizacion de Saldo Guia de Compra', 'guiaCompra' => $context->guiaCompra->getDocumento()]);

        Log::info('GuiaCompraHandler', ['message' => 'Finaliza Proceso']);
    }

    /**
     * Envía la guía de compra al sistema WMS.
     *
     * @param string $guiaCompra Número de la guía de compra.
     * @return mixed Respuesta del WMS.
     * @throws GuiaCompraException Si hay un error al enviar la guía de compra.
     */
    public function enviaGuiaCompraWMS($context)
    {
        try {

            $guiaCompra = $context->guiaCompra->getDocumento('gui_numero');

            $url = url('/WMS/CreateOrdenEntrada');

            Http::post($url, ['guiaCompra' => $guiaCompra]);
        } catch (Exception $e) {

            Log::error('Error al obtener la ruta: ' . $e->getMessage());
        }
    }
}
