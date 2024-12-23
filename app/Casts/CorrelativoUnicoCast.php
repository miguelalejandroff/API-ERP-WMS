<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Support\Facades\Log;


class CorrelativoUnicoCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return mixed
     */

    public static function generateConcatenation($folio, $ultimoDigito)
    {
        Log::info("Folio: $folio, Ultimo Digito: $ultimoDigito");
        $correlativo = strtotime(now());
        return $folio . $ultimoDigito . $correlativo;
    }

    public function get($model, string $key, $value, array $attributes)
    {
        $folio = $model->ped_folio;
        $ultimoDigito = $model->ped_codrub;
        
        return self::generateConcatenation($folio, $ultimoDigito);

    }
    //referencia pedidosdetalles

    /**
     * Prepare the given value for storage.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return mixed
     */
    public function set($model, string $key, $value, array $attributes)
    {
        return $value;
    }
    // referencia pedidosdetalles
}
