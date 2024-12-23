<?php

namespace App\Http\Controllers;

use App\Models\cmsalbod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SaldoBodegaController extends Controller
{ 
    public function crearSaldoBodega($codigoProducto, $bodegaDestino, $cantidad, $year)
    {
        // Verificar si existe el saldoBodega
        cmsalbod::byBodegaProducto($codigoProducto, $bodegaDestino, $year);
        

            cmsalbod::updateOrCreate(
                [
                'bod_ano' => $year,
                'bod_produc' => $codigoProducto,
                'bod_bodega' => $bodegaDestino,
                'bod_salini' => 0,
                'bod_stockb' => $cantidad,
                'bod_stolog' => $cantidad,
                'bod_storep' => 0,
                'bod_stomax' => 0
                ]
            );
    }
}
