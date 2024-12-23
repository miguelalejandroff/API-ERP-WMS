<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Support\Facades\Log;

class CorrelativoRecepcionCast implements CastsAttributes
{
    /**
     * Obtiene el valor transformado del atributo.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return string
     */
    public function get($model, string $key, $value, array $attributes)
    {
        if (config('app.debug')) {
            Log::info('Inicio de CorrelativoRecepcionCast - get');
            Log::info('Contenido del array en CorrelativoRecepcionCast:', compact('attributes'));
        }

        $serie = $attributes['gui_numero'] ?? '';
        $tipo = $attributes['gui_tipgui'] ?? '';

        // Retorna la combinación de atributos
        return $serie . $tipo;
    }

    /**
     * Prepara el valor para ser almacenado.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return mixed
     */
    public function set($model, string $key, $value, array $attributes)
    {
        if (config('app.debug')) {
            Log::info('Fin de CorrelativoRecepcionCast - set');
            Log::info('Contenido del array en CorrelativoRecepcionCast:', $attributes);
        }

        // Retorna el valor sin cambios
        return $value;
    }
}
