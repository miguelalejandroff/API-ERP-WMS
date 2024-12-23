<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Support\Facades\Log;

class CorrelativoUnicoCast implements CastsAttributes
{
    /**
     * Genera un correlativo único concatenando folio, último dígito y marca de tiempo.
     *
     * @param  string|null  $folio
     * @param  string|null  $ultimoDigito
     * @return string
     */
    public static function generateConcatenation($folio, $ultimoDigito)
    {
        // Validación básica de entrada
        $folio = $folio ?? '';
        $ultimoDigito = $ultimoDigito ?? '';

        // Generación del correlativo único
        $correlativo = now()->timestamp;

        if (config('app.debug')) {
            Log::info("Generando correlativo único", [
                'folio' => $folio,
                'ultimo_digito' => $ultimoDigito,
                'correlativo' => $correlativo,
            ]);
        }

        return $folio . $ultimoDigito . $correlativo;
    }

    /**
     * Transforma el atributo al recuperarlo.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return string
     */
    public function get($model, string $key, $value, array $attributes)
    {
        $folio = $model->ped_folio ?? null;
        $ultimoDigito = $model->ped_codrub ?? null;

        return self::generateConcatenation($folio, $ultimoDigito);
    }

    /**
     * Prepara el atributo para ser almacenado.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return mixed
     */
    public function set($model, string $key, $value, array $attributes)
    {
        // Aquí puedes realizar transformaciones adicionales si es necesario
        return $value;
    }
}
