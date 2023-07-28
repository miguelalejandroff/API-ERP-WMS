<?php

namespace App\ERP\Handler;

use App\Models\cmproductos;
use Carbon\Carbon;
use Exception;

class ActualizaProductoHandler
{
    /**
     * Enviar datos de la guia de compra
     * 
     */
    public function handle($producto, $precio, $cantidad, $fecha, $documento, $cantidadRecepcionada)
    {

        $array = [];
        $producto = $this->producto($producto);

        $precio = round($precio, 2);
        $cantidad =  round($cantidad, 2);

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

        if ($producto->pro_stockp < 0) {
            $costoMedio = $precio;
        } else {
            $costoMedio = (($cantidad * $precio) + ($producto->pro_stockp * $producto->pro_cosmed)) / ($cantidad + $producto->pro_stockp);
        }

        $array['pro_stockp'] = round($producto->pro_stockp + $cantidadRecepcionada, 2);
        $array['pro_cosmed'] = round($costoMedio, 2);

        $producto->update($array);
    }
    protected function producto($producto)
    {
        return cmproductos::where('pro_codigo', $producto)->where('pro_anomes', Carbon::now()->format('Ym'))->first();
    }
}
