<?php

namespace App\Libs;

use App\Enums\SaldoBodegaEnum;
use App\Models\cmbodega;
use App\Models\cmdetgui;
use App\Models\cmguias;
use Carbon\Carbon;
use Closure;
use Exception;

/**
 * La clase GuiaRecepcion maneja la lógica relacionada con la creación y actualización de guías de recepción y el saldo de bodega en un sistema de gestión de bodegas.
 */
class GuiaRecepcion
{
    /**
     * Constructor de la clase GuiaRecepcion.
     *
     * @param object $recepcion El objeto de recepción.
     * @param object $recepcionDetalle El objeto de detalle de la recepción.
     * @param object $ordenCompra El objeto de orden de compra.
     * @param object $proveedor El objeto del proveedor.
     * @param int $cantidadPromo La cantidad de productos promocionales. Valor predeterminado: 0.
     * @param int $cantidadNormal La cantidad de productos normales. Valor predeterminado: 0.
     * @param Closure $catch Closure para manejar errores.
     * @param string $tipoGuia El tipo de guía. Valor predeterminado: "08".
     * @param int $bodegaOrigen El ID de la bodega de origen. Valor predeterminado: 29.
     * @param int $sucursalOrigen El ID de la sucursal de origen. Valor predeterminado: 0.
     * @param int|null $sucursalDestino El ID de la sucursal de destino. Valor predeterminado: null.
     */
    public function __construct(
        protected $recepcion,
        protected $recepcionDetalle,
        protected $ordenCompra,
        protected $proveedor,
        protected $cantidadPromo = 0,
        protected $cantidadNormal = 0,
        Closure $catch,
        protected $tipoGuia = "08",
        protected $bodegaOrigen = 29,
        protected $sucursalOrigen = 0,
        protected $sucursalDestino = null,
    ) {

        try {

            $correlativo = $this->correlativo() - 1;
            $sucursalDestino = cmbodega::Bodega($recepcion->bodegaDestino)->bod_codsuc;

            if ($cantidadNormal > 0) {
                $correlativo += 1;
                $this->insertEncabezado($correlativo, $recepcion, $proveedor);
            }
            if ($cantidadPromo > 0) {
                $this->insertEncabezado($correlativo + 1, $recepcion, $proveedor);
            }

            foreach ($recepcionDetalle as $key => $row) {
                $detalleOrden = $this->buscaProducto($ordenCompra->cmdetord, $row);

                if (!$row->promocion) {

                    $this->insertDetalle($correlativo, $detalleOrden, $recepcion, $row);
                } else {

                    $this->insertDetalle($correlativo + 1, $detalleOrden, $recepcion,  $row);
                }

                new SaldoBodega($bodegaOrigen, $row->codigoProducto, $row->cantidadRecepcionada, SaldoBodegaEnum::DECREMENT, function ($message) {
                    throw new Exception($message, 500);
                });

                new SaldoBodega($recepcion->bodegaDestino, $row->codigoProducto, $row->cantidadRecepcionada, SaldoBodegaEnum::INCREMENT, function ($message) {
                    throw new Exception($message, 500);
                });
            }
        } catch (Exception $e) {
            $catch($e->getMessage());
        }
    }

    /**
     * Inserta un encabezado de guía de recepción en la base de datos.
     *
     * @param int $id El ID de la guía.
     * @param object $recepcion El objeto de recepción.
     * @param object $proveedor El objeto del proveedor.
     * 
     * @return object Retorna el resultado de la operación de inserción.
     */
    public function insertEncabezado($id, $recepcion, $proveedor)
    {
        return cmguias::create([
            "gui_numero" => $id, //mismo numero de la solicitud de recepcion $recepcion->solicitudRecepcion
            "gui_tipgui" => $this->tipoGuia,
            "gui_fechag" => WMS::date($recepcion->fechaRecepcionWMS, WMS::DATE_FORMAT_WMS, WMS::DATE_FORMAT_DATE),
            "gui_ordcom" => $recepcion->numeroOrden,
            "gui_numrut" => $proveedor->aux_numrut,
            "gui_digrut" => $proveedor->aux_digrut,
            "gui_subcta" => $proveedor->aux_claves,
            "gui_nombre" => $proveedor->aux_nombre,
            "gui_guipro" => $recepcion->guiaProveedor,
            "gui_facpro" => $recepcion->facturaProveedor,
            "gui_facals" => $recepcion->facturaCals,
            "gui_sucori" => $this->sucursalOrigen,
            "gui_sucdes" => $this->sucursalDestino,
            "gui_paract" => "N",
            "gui_fecmod" => WMS::date($recepcion->fechaRecepcionWMS, WMS::DATE_FORMAT_WMS, WMS::DATE_FORMAT_DATE),
            "gui_codusu" => $recepcion->usuario,
            "gui_empres" => "1",
        ]);
    }

    /**
     * Inserta un detalle de guía de recepción en la base de datos.
     *
     * @param int $id El ID de la guía.
     * @param object $detalleOrden El objeto de detalle de la orden.
     * @param object $recepcion El objeto de recepción.
     * @param object $row El objeto de fila (row).
     * 
     * @return object Retorna el resultado de la operación de inserción.
     */
    public function insertDetalle($id, $detalleOrden, $recepcion,  $row)
    {
        return cmdetgui::create([
            "gui_numero" => $id,
            "gui_tipgui" => $this->tipoGuia,
            "gui_bodori" => $recepcion->bodegaOrigen,
            "gui_boddes" => $recepcion->bodegaDestino,
            "gui_produc" => $row->codigoProducto,
            "gui_descri" => $detalleOrden->ord_descri,
            "gui_canord" => $row->cantidadSolicitada,
            "gui_canrep" => $row->cantidadRecepcionada,
            "gui_preuni" => $detalleOrden->calculaCosto->precioCalculado,
        ]);
    }

    /**
     * Busca un producto específico en el detalle de la compra.
     *
     * @param object $compraDetalle El objeto de detalle de compra.
     * @param object $detalle El objeto de detalle.
     * 
     * @return object Retorna el producto encontrado.
     */
    protected function buscaProducto($compraDetalle, $detalle)
    {
        return $compraDetalle->filter(function ($item) use ($detalle) {
            return $item->ord_produc == $detalle->codigoProducto;
        })->first();
    }

    /**
     * Calcula el siguiente correlativo para las guías de recepción.
     *
     * @return int Retorna el nuevo correlativo calculado.
     */
    protected function correlativo()
    {
        // busca el ultimo correlativo de las guias de recepcion
        $ultimoCorrelativo = cmguias::whereIn('gui_tipgui', ["01", "08"])->whereMonth('gui_fechag', now()->month)->whereYear('gui_fechag', now()->year)->max('gui_numero');
        // fecha actual del sistema
        $fechaActual = Carbon::now()->format('ym');
        // si el correlativo inicial es null inicializa el correlativo en 000
        if (is_null($ultimoCorrelativo)) {
            $ultimoCorrelativo = 0;
        }
        // borra el año y mes en el ultimo correlativo
        $ultimoCorrelativo = preg_replace("/^{$fechaActual}/", '', $ultimoCorrelativo);
        //str_replace($fechaActual, "", $ultimoCorrelativo);

        // si los ultimos digitos del correlativo obtenido son menores a 999 
        if ($ultimoCorrelativo < 999) {
            // suma 1 al correlativo y le agrega 0 al comienzo
            $nuevoCorrelativo =  str_pad((int)$ultimoCorrelativo + 1, 3, '0', STR_PAD_LEFT);
        } else {
            // suma 1 al correlativo
            $nuevoCorrelativo = (int)$ultimoCorrelativo + 1;
        }

        return (int) ($fechaActual . $nuevoCorrelativo);
    }
}
