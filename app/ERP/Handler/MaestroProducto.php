<?php

namespace App\ERP\Handler;

use App\Models\cmproductos;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;

/**
 * Class MaestroProducto
 *
 * Esta clase maneja la actualización del maestro producto.
 */
class MaestroProducto
{

    /**
     * Actualiza los datos del maestro producto.
     */
    public function updated(
        $producto,
        $precio,
        $cantidad,
        $fecha,
        $documento,
        $cantidadRecepcionada
    ) {
        try {
            $producto = cmproductos::byProducto($producto);
            $precio = round($precio, 2);
            $cantidad = round($cantidad, 2);
            $cantidadRecepcionada = round($cantidadRecepcionada, 2);

            $this->getUpdatedFields($producto, $precio, $fecha, $documento, $cantidad, $cantidadRecepcionada);
        } catch (Exception $e) {
            throw new $e;
        }
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
        try {
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

            $producto->update($array);
        } catch (Exception $e) {

            throw new $e;
        }
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
