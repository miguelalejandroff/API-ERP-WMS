<?php

namespace App\ERP\Handler;

use App\Models\cmproductos;
use Carbon\Carbon;

/**
 * Class MaestroProducto
 *
 * Esta clase maneja la actualización del producto maestro.
 */
class MaestroProducto
{

    /**
     * Actualiza los datos del producto maestro.
     */
    protected function updateProductoMaestro(
        $producto,
        $precio,
        $cantidad,
        $fecha,
        $documento,
        $cantidadRecepcionada
    ) {
        $array = [];
        $producto = cmproductos::byProducto($producto);

        $precio = round($precio, 2);
        $cantidad = round($cantidad, 2);

        $array = $this->getUpdatedFields($producto, $precio, $fecha, $documento, $cantidad, $cantidadRecepcionada);
        $producto->update($array);
    }

    /**
     * Obtiene los campos actualizados para el producto.
     *
     * @param $producto
     * @param $precio
     * @param $fecha
     * @param $documento
     * @param $cantidad
     * @return array
     */
    protected function getUpdatedFields($producto, $precio, $fecha, $documento, $cantidad, $cantidadRecepcionada)
    {
        $array = [];
        if ($precio > (int)$producto->pro_comaal) {
            $array['pro_comaal'] = $precio;
            $array['pro_femaal'] = $fecha;
            $array['pro_domaal'] = $documento;
        }

        $fechaActual = Carbon::parse($fecha);
        $fechaUltimaCompra = Carbon::parse($producto->pro_feulco);

        if ($fechaActual->gte($fechaUltimaCompra) || is_null($producto->pro_feulco)) {
            $array['pro_coulco'] = $precio;
            $array['pro_feulco'] = $fecha;
            $array['pro_doulco'] = $documento;
        }

        $costoMedio = $this->calcularCostoMedio($producto, $precio, $cantidad);
        $array['pro_stockp'] = round($producto->pro_stockp + $cantidadRecepcionada, 2);
        $array['pro_cosmed'] = round($costoMedio, 2);

        return $array;
    }

    /**
     * Calcula el costo medio para el producto.
     *
     * @param $producto
     * @param $precio
     * @param $cantidad
     * @return float
     */
    protected function calcularCostoMedio($producto, $precio, $cantidad)
    {
        if ($producto->pro_stockp < 0) {
            return $precio;
        }

        return (($cantidad * $precio) + ($producto->pro_stockp * $producto->pro_cosmed)) / ($cantidad + $producto->pro_stockp);
    }
}
