<?php

namespace App\Libs;

use App\Models\pedidosdetalles;
use Carbon\Carbon;

class CorrelativoUnico
{
    public function __construct($folio, $rubro) {

    }
    public static function generarCorrelativo()
    {
        $pedido = pedidosdetalles::where('ped_estped', 'A')
            ->first();

        if (!$pedido) {

            return null;
        }

        $folio = $pedido->ped_folio;
        $rubro = $pedido->ped_codrub;
        $correlativo = strtotime(now());

        return $folio . '-' . $rubro . '-' . $correlativo;
        //split, array
        // setter getter laravel modelos
    }
}
