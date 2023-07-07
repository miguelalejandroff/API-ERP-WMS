<?php

namespace App\Libs;

use App\Enums\SaldoBodegaEnum;
use App\Models\guicompra;
use App\Models\guidetcompra;
use Closure;
use Exception;

/**
 * Clase para manejar la creación de guías de compra a partir de la recepción de mercancía
 */
class GuiaCompra
{
    /**
     * Constructor de la clase GuiaCompra
     *
     * @param mixed $recepcion Objeto que representa la recepción de la mercancía
     * @param mixed $recepcionDetalle Objeto que representa el Detalles de la recepción de la mercancía
     * @param mixed $ordenCompra Objeto que representa la orden de compra
     * @param mixed $proveedor Objeto que representa el proveedor
     * @param Closure $catch Función para manejar excepciones
     * @param string $tipoGuia Tipo de guía de compra
     * @param int $bodegaOrigen Identificador de la bodega de origen
     * @param int $bodegaDestino Identificador de la bodega de destino
     * @param int $sucursalOrigen Identificador de la sucursal de origen
     * @param int $sucursalDestino Identificador de la sucursal de destino
     */
    public function __construct(
        protected $recepcion,
        protected $recepcionDetalle,
        protected $ordenCompra,
        protected $proveedor,
        protected Closure $catch,
        protected $tipoGuia = "07",
        protected $bodegaOrigen  = 0,
        protected $bodegaDestino  = 29,
        protected $sucursalOrigen = 0,
        protected $sucursalDestino = 1
    ) {
        try {
            $guiaCompra = $this->buscarEncabezado($recepcion->numeroOrden);


            if (!$guiaCompra) {

                $encabezado =  $this->insertEncabezado($recepcion, $ordenCompra, $proveedor);

                foreach ($ordenCompra->cmdetord as $key => $row) {

                    $detalle = $this->insertDetalle($encabezado->gui_clave, $row);

                    $productoRecepcion = $this->buscaProducto($recepcionDetalle, $detalle);

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
                    /*
                    new SaldoBodega($bodegaDestino, $row->ord_produc, $row->calculaCosto->cantidadCalculada, SaldoBodegaEnum::INCREMENT, function ($message) {
                        throw new Exception($message, 500);
                    });
                    */
                }
            }

            foreach ($recepcionDetalle  as  $key =>  $row) {
                $detalle = $this->buscaDetalle($recepcion->numeroOrden, $row->codigoProducto);

                $detalle->decrement('gui_saldo', $row->cantidadRecepcionada);
            }
        } catch (Exception $e) {
            $catch($e->getMessage());
        }
    }

    /**
     * Inserta un encabezado de guía de compra en la base de datos
     *
     * @param mixed $recepcion Objeto que representa la recepción de la mercancía
     * @param mixed $orden Objeto que representa la orden de compra
     * @param mixed $proveedor Objeto que representa el proveedor
     *
     * @return mixed El encabezado de la guía de compra insertado
     */
    public function insertEncabezado($recepcion, $orden, $proveedor)
    {

        return guicompra::create([
            "gui_numero" => $orden->ord_numcom,
            "gui_tipgui" => $this->tipoGuia,
            "gui_fechag" => WMS::date($recepcion->fechaRecepcionWMS, WMS::DATE_FORMAT_WMS, WMS::DATE_FORMAT_DATE),
            "gui_ordcom" => $orden->ord_numcom,
            "gui_numrut" => $proveedor->aux_numrut,
            "gui_digrut" => $proveedor->aux_digrut,
            "gui_subcta" => $proveedor->aux_claves,
            "gui_nombre" => $proveedor->aux_nombre,
            "gui_guipro" => $recepcion->guiaProveedor,
            "gui_facpro" => $recepcion->facturaProveedor,
            "gui_facals" => $recepcion->facturaCals,
            "gui_sucori" => $this->sucursalOrigen,
            "gui_sucdes" => $this->sucursalDestino, //cmbodega::Bodega($wms->bodegaDestino)->bod_codsuc,
            "gui_paract" => "N",
            "gui_fecmod" => WMS::date($recepcion->fechaRecepcionWMS, WMS::DATE_FORMAT_WMS, WMS::DATE_FORMAT_DATE),
            "gui_codusu" => $recepcion->usuario,
            "gui_empres" => "1",
            "gui_current" => WMS::date($recepcion->fechaRecepcionWMS, WMS::DATE_FORMAT_WMS, WMS::DATE_FORMAT_CURRENT),
        ]);
    }

    /**
     * Inserta un detalle de guía de compra en la base de datos
     *
     * @param int $id Identificador de la guía de compra
     * @param mixed $row Fila de la orden de compra
     *
     * @return mixed El detalle de la guía de compra insertado
     */
    public function insertDetalle($id,  $row)
    {
        return guidetcompra::create([
            "gui_clave" => $id,
            "gui_numero" => $row->ord_numcom,
            "gui_tipgui" => $this->tipoGuia,
            "gui_bodori" => $this->bodegaOrigen,
            "gui_boddes" => $this->bodegaDestino,
            "gui_produc" => $row->ord_produc,
            "gui_descri" => $row->ord_descri,
            "gui_unimed" => $row->ord_unimed,
            "gui_canord" => $row->calculaCosto->cantidadCalculada,
            "gui_canrep" => $row->calculaCosto->cantidadCalculada,
            "gui_preuni" => $row->calculaCosto->precioCalculado,
            "gui_saldo" => $row->calculaCosto->cantidadCalculada,
        ]);
    }
    /**
     * Busca un producto en los detalles de la recepción de mercancía
     *
     * @param mixed $recepcionDetalle Detalles de la recepción de la mercancía
     * @param mixed $detalle Detalle de la guía de compra
     *
     * @return mixed El producto buscado
     */
    protected function buscaProducto($recepcionDetalle, $detalle)
    {
        return $recepcionDetalle->filter(function ($item) use ($detalle) {
            return $item->codigoProducto == $detalle->gui_produc;
        })->first();
    }
    /**
     * Busca el encabezado de una guía de compra por su número de orden
     *
     * @param string $orden Número de orden de la guía de compra
     *
     * @return mixed El encabezado de la guía de compra buscada
     */
    protected function buscarEncabezado($orden)
    {
        return guicompra::where('gui_ordcom', $orden)->first();
    }

    /**
     * Busca un detalle de una guía de compra por su número de orden y el código del producto
     *
     * @param string $orden Número de orden de la guía de compra
     * @param string $producto Código del producto a buscar
     *
     * @return mixed El detalle de la guía de compra buscado
     */
    protected function buscaDetalle($orden, $producto)
    {
        return guidetcompra::where('gui_numero', $orden)->where('gui_produc', $producto);
    }
}
