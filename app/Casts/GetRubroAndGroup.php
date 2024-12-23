<?php

namespace App\Casts;

use App\Models\Grupos;
use App\Models\Rubros;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class GetRubroAndGroup implements CastsAttributes
{
    /**
     * Transforma el atributo al recuperarlo.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return array
     */
    public function get($model, string $key, $value, array $attributes)
    {
        $result = [
            'codigoRubro' => null,
            'nombreRubro' => null,
            'codigoGrupo' => null,
            'nombreGrupo' => null,
        ];

        // Validar el atributo 'pro_newcod'
        if (isset($attributes['pro_newcod']) && strlen($attributes['pro_newcod']) >= 2) {
            $array = str_split($attributes['pro_newcod']);

            // Buscar el rubro
            $rubros = Rubros::where('cod_rubro', $array[0])->first();
            $result['codigoRubro'] = $rubros?->cod_rubro;
            $result['nombreRubro'] = $rubros?->rubro;

            // Buscar el grupo
            $grupo = Grupos::where('cod_rubro', $array[0])
                ->where('cod_grupo', $array[1])
                ->first();
            $result['codigoGrupo'] = $grupo?->cod_rg;
            $result['nombreGrupo'] = $grupo?->grupo;
        }

        return $result;
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
        // Aquí podrías implementar lógica para transformar el valor antes de almacenarlo
        return $value;
    }
}
