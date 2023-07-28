<?php

namespace App\ERP\Handler;

use App\ERP\Build\Handler;
use App\ERP\Exception\GuiaCompraException;
use App\Models\guicompra;
use App\Models\guidetcompra;
use App\WMS\Contracts\OrdenEntradaService;
use App\WMS\EndpointWMS;
use Exception;

/**
 * Clase GuiaCompraHandler
 * 
 * Esta clase se encarga de manejar la creación de guías de compra basándose en la información de la recepción de mercancía.
 * Hereda de la clase Handler.
 */
class GuiaCompraHandler extends Handler
{

    /**
     * @var string $tipoGuia define el tipo de guía de compra. Por defecto es "07".
     */
    private $tipoGuia = "07";

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

    /**
     * @var string $parametro define un parámetro de defecto. Por defecto es "N".
     */
    private $parametro = "N";

    /**
     * @var string $empresa define el ID de la empresa. Por defecto es 1.
     */
    private $empresa = 1;

    /**
     * @var string $ordenCompra define la orden de compra.
     */
    private $ordenCompra;

    /**
     * @var string $solicitudRecepcion  define la solicitud de recepción creada por el ERP.
     */
    private $solicitudRecepcion;

    /**
     * @var string $recepcion define la recepción creada por el WMS.
     */
    private $recepcion;

    /**
     * @var string $proveedor define el proveedor.
     */
    private $proveedor;

    /**
     * Método handle
     * 
     * Este método maneja la lógica principal de la creación de la guía de compra. 
     * Recibe un objeto contexto que contiene información relevante para la creación de la guía.
     * 
     * @param object $context objeto que contiene la orden de compra, la solicitud de recepción, la recepción y el proveedor.
     */
    public function handle($context)
    {

        try {
            $ordenCompra = $context->ordenCompra;

            $solicitudRecepcion = $context->solicitudRecepcion;

            $recepcion = $context->recepcion;

            $proveedor = $context->proveedor;

            $guiaCompra = guicompra::where('gui_ordcom', $ordenCompra->ord_numcom)->where('gui_tipgui', $this->tipoGuia)->first();

            if (!$guiaCompra) {

                $encabezado =  $this->insertEncabezado($solicitudRecepcion, $recepcion, $ordenCompra, $proveedor);

                foreach ($ordenCompra->cmdetord as &$row) {

                    $detalle = $this->insertDetalle($encabezado->gui_clave, $row);

                    /*
                $productoRecepcion = $recepcion->detalle->filter(function ($item) use ($detalle) {
                    return $item->codigoProducto == $detalle->gui_produc;
                })->first();

                $cantidadRecepcionada = 0;

                if ($productoRecepcion) {
                    $cantidadRecepcionada = $productoRecepcion->cantidadRecepcionada;
                }

                new MaestroProducto(
                    $detalle->gui_produc,
                    $detalle->gui_preuni,
                    $detalle->gui_canord,
                    $encabezado->gui_fechag,
                    $detalle->gui_numero,
                    $cantidadRecepcionada,
                    function ($message) {
                        throw new Exception($message, 500);
                    }
                );
                */
                }
                //$this->enviaGuiaCompraWMS($encabezado->gui_numero);
            }
            /*
            foreach ($recepcion->detalle as $row) {
                guidetcompra::where('gui_numero', $recepcion->numeroOrden)
                ->where('gui_tipgui', $this->tipoGuia)
                ->where('gui_produc', $row->codigoProducto)
                ->decrement('gui_saldo', $row->cantidadRecepcionada);
            }
            */
        } catch (GuiaCompraException $e) {
            throw $e;  // Re-lanza la excepción para que pueda ser atrapada en el nivel superior
        }
    }

    /**
     * Método insertEncabezado
     * 
     * Este método inserta un encabezado de la guía de compra en la base de datos.
     * 
     * @param object $solicitudRecepcion objeto que representa la solicitud de recepción creada por el ERP.
     * @param object $recepcion objeto que representa la recepción creada por el WMS.
     * @param object $ordenCompra objeto que representa la orden de compra.
     * @param object $proveedor objeto que representa el proveedor.
     * 
     * @return object retorna el encabezado de la guía de compra que fue insertado en la base de datos.
     */
    public function insertEncabezado($solicitudRecepcion, $recepcion, $ordenCompra, $proveedor)
    {

        try {

            $guiaCompra = new guicompra;

            $guiaCompra->gui_numero = $ordenCompra->ord_numcom;
            $guiaCompra->gui_tipgui = $this->tipoGuia;
            $guiaCompra->gui_fechag = $recepcion->fechaRecepcionWMS->format('Y-m-d');
            $guiaCompra->gui_ordcom = $ordenCompra->ord_numcom;

            $guiaCompra->gui_numrut = $proveedor->aux_numrut;
            $guiaCompra->gui_digrut = $proveedor->aux_digrut;
            $guiaCompra->gui_subcta = $proveedor->aux_claves;
            $guiaCompra->gui_nombre = $proveedor->aux_nombre;

            $guiaCompra->gui_guipro = $solicitudRecepcion->gui_guipro ?? 0;
            $guiaCompra->gui_facpro = $solicitudRecepcion->gui_facpro ?? 0;
            $guiaCompra->gui_facals = $solicitudRecepcion->gui_facals ?? 0;

            $guiaCompra->gui_sucori = $this->sucursalOrigen;
            $guiaCompra->gui_sucdes = $this->sucursalDestino;

            $guiaCompra->gui_paract = $this->parametro;
            $guiaCompra->gui_fecmod = $recepcion->fechaRecepcionWMS->format('Y-m-d');
            $guiaCompra->gui_codusu = $solicitudRecepcion->gui_codusu;
            $guiaCompra->gui_empres = $this->empresa;

            $guiaCompra->gui_current = $recepcion->fechaRecepcionWMS->format('Y-m-d H:i');

            $guiaCompra->saveOrFail();

            return $guiaCompra;
        } catch (Exception $e) {
            throw new GuiaCompraException("Error al Insertar el Encabezado de la Guia de Compra: {$guiaCompra->gui_numero}");
        }
    }


    /**
     * Método insertDetalle
     * 
     * Este método inserta un detalle de la guía de compra en la base de datos.
     * 
     * @param int $id identificador de la guía de compra.
     * @param object $row fila de la orden de compra.
     * 
     * @return object retorna el detalle de la guía de compra que fue insertado en la base de datos.
     */
    public function insertDetalle($id,  $row)
    {
        try {

            $detalleCompra = new guidetcompra();

            $detalleCompra->gui_clave = $id;
            $detalleCompra->gui_numero = $row->ord_numcom;
            $detalleCompra->gui_tipgui = $this->tipoGuia;

            $detalleCompra->gui_bodori = $this->bodegaOrigen;
            $detalleCompra->gui_boddes = $this->bodegaDestino;

            $detalleCompra->gui_produc = $row->ord_produc;
            $detalleCompra->gui_descri = $row->ord_descri;
            $detalleCompra->gui_unimed = $row->ord_unimed;

            $detalleCompra->gui_canord = $row->calculaCosto->cantidadCalculada;
            $detalleCompra->gui_canrep = $row->calculaCosto->cantidadCalculada;
            $detalleCompra->gui_preuni = $row->calculaCosto->precioCalculado;
            $detalleCompra->gui_saldo = $row->calculaCosto->cantidadCalculada;

            $detalleCompra->saveOrFail();

            return $detalleCompra;
        } catch (Exception $e) {
            throw new GuiaCompraException("Error al Insertar el Detalle de la Guia de Compra: {$detalleCompra->gui_numero}");
        }
    }

    public function enviaGuiaCompraWMS($guiaCompra)
    {

        try {
            // Aquí debes asegurarte de que app()->request tenga los datos necesarios
            // Puedes hacer algo como esto:
            app()->request->merge(['guiaCompra' => $guiaCompra]);

            // Obten la instancia de OrdenEntradaService a través del contenedor de servicios
            $orden = app(OrdenEntradaService::class);

            // Crea una instancia del controlador de orden
            $ordenController = app(EndpointWMS::class);

            // Ahora puedes usar $orden en el método createOrdenEntrada
            $response = $ordenController->createOrdenEntrada($orden);

            return $response;
        } catch (Exception $e) {
            throw new GuiaCompraException("Error al Enviar la Guia de Compra al WMS: {$response}");
        }
    }
}
