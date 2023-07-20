<?php

namespace App\ERP\Handler;

use App\ERP\Build\Handler;
use App\Http\Controllers\MaestroProducto;
use App\Libs\WMS;
use App\Models\guicompra;
use App\Models\guidetcompra;
use Exception;

/**
 * Clase para manejar la creación de guías de compra a partir de la recepción de mercancía
 */
class GuiaCompraHandler extends Handler
{

    private $tipoGuia = "07";

    private $bodegaOrigen  = 0;

    private $bodegaDestino  = 29;

    private $sucursalOrigen = 0;

    private $sucursalDestino = 1;

    private $parametro = "N";

    private $empresa = 1;

    public function handle($context)
    {

        $ordenCompra = $context->ordenCompra;

        $solicitudRecepcion = $context->solicitudRecepcion;

        $recepcion = $context->recepcion;

        $proveedor = $context->proveedor;

        $guiaCompra = guicompra::where('gui_ordcom', $ordenCompra->ord_numcom)->where('gui_tipgui', $this->tipoGuia)->first();

        if (!$guiaCompra) {

            $encabezado =  $this->insertEncabezado($solicitudRecepcion, $recepcion, $ordenCompra, $proveedor);

            foreach ($ordenCompra->cmdetord as $row) {
                /*if ($row->cmproducto->rubro == "3") {
                    $cantid_ord = 0;

                    // Asume que tienes una conexión a la base de datos configurada y lista para usar.
                    // Este es solo un ejemplo, probablemente necesites ajustarlo para tu situación específica.

                    // $query = $db->prepare("SELECT ord_cantid FROM cmdetord WHERE ord_produc = :produc_gc AND ord_numcom = :gui_ordcom");
                    // $query->execute([':produc_gc' => $produc_gc, ':gui_ordcom' => $guias['gui_ordcom']]);
                    // $result = $query->fetch(PDO::FETCH_ASSOC);

                    if ($result) {
                        $cantid_ord = $result['ord_cantid'];
                    }

                    if ($canord_gc != $cantid_ord) {
                        $canord_gc = $cantid_ord;
                    }
                }*/
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
        }

        foreach ($recepcion->detalle as $row) {
            guidetcompra::where('gui_numero', $recepcion->numeroOrden)
                ->where('gui_tipgui', $this->tipoGuia)
                ->where('gui_produc', $row->codigoProducto)
                ->decrement('gui_saldo', $row->cantidadRecepcionada);
        }
    }

    /**
     * Inserta un encabezado de guía de compra en la base de datos
     *
     * @param mixed $solicitudRecepcion Objeto que representa la recepción de la mercancía
     * @param mixed $recepcion Objeto que representa la recepción de la mercancía
     * @param mixed $orden Objeto que representa la orden de compra
     * @param mixed $proveedor Objeto que representa el proveedor
     *
     * @return mixed El encabezado de la guía de compra insertado
     */
    public function insertEncabezado($solicitudRecepcion, $recepcion, $orden, $proveedor)
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
            "gui_guipro" => $solicitudRecepcion->gui_guipro,
            "gui_facpro" => $solicitudRecepcion->gui_facpro,
            "gui_facals" => $solicitudRecepcion->gui_facals,
            "gui_sucori" => $this->sucursalOrigen,
            "gui_sucdes" => $this->sucursalDestino, //cmbodega::Bodega($wms->bodegaDestino)->bod_codsuc,
            "gui_paract" => $this->parametro,
            "gui_fecmod" => WMS::date($recepcion->fechaRecepcionWMS, WMS::DATE_FORMAT_WMS, WMS::DATE_FORMAT_DATE),
            "gui_codusu" => $solicitudRecepcion->gui_codusu,
            "gui_empres" => $this->empresa,
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
}
