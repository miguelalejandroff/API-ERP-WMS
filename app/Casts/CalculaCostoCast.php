<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use App\Libs\CalcularCosto;

class CalculaCostoCast implements CastsAttributes
{
    /**
     * Transform the attribute when retrieving it.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return mixed
     */
    public function get($model, string $key, $value, array $attributes)
    {
        if (!isset($model->cmordcom)) {
            throw new \Exception("El atributo 'cmordcom' es necesario para calcular el costo.");
        }

        return new CalcularCosto($model->cmordcom, $model);
    }

    /**
     * Prepare the attribute for storage.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return mixed
     */
    public function set($model, string $key, $value, array $attributes)
    {
        // Si necesitas procesar el valor antes de almacenarlo, hazlo aquí
        return $value;
    }
}
