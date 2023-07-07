<?php

namespace App\Libs;

use App\Logs\Log;
use App\Models\cmproductos;
use Carbon\Carbon;
use Closure;
use Exception;
use Illuminate\Support\Facades\DB;

class MaestroProducto
{
    /**
     * Enviar datos de la guia de compra
     * 
     */
    public function __construct(
        protected $producto,
        protected $precio,
        protected $cantidad,
        protected $fecha,
        protected $documento,
        protected $cantidadRecepcionada,
        Closure $catch
    ) {

        try {
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
        } catch (Exception $e) {
            $catch($e->getMessage());
        }
    }
    protected function producto($producto)
    {
        return cmproductos::where('pro_codigo', $producto)->where('pro_anomes', Carbon::now()->format('Ym'))->first();
    }
}
