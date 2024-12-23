<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use App\Libs\CorrelativoUnico;
use Illuminate\Support\Facades\Log;

Log::info('Prueba de log en CorrelativoRecepcionCast');

class CorrelativoRecepcionCast implements CastsAttributes
{
    
    public function get($model, string $key, $value, array $attributes)
    {
        Log::info('Inicio de CorrelativoRecepcionCast - get');
        Log::info('Contenido del array en CorrelativoRecepcionCast:', compact('attributes'));
    
        $serie = $attributes['gui_numero'] ?? '';
        $tipo = $attributes['gui_tipgui'] ?? '';
    
        return $serie . $tipo;
    }
    
    

    public function set($model, string $key, $value, array $attributes)
    {
        Log::info('Fin de CorrelativoRecepcionCast - get');
        Log::info('Contenido del array en CorrelativoRecepcionCast:', $attributes);

        return $value;
    }
}
