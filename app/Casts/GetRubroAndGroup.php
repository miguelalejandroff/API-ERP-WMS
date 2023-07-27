<?php

namespace App\Casts;

use App\Models\grupos;
use App\Models\rubros;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class GetRubroAndGroup implements CastsAttributes
{
    public $codigoRubro;
    public $nombreRubro;
    public $codigoGrupo;
    public $nombreGrupo;
    /**
     * Cast the given value.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return mixed
     */
    public function get($model, string $key, $value, array $attributes)
    {
        $array = str_split($attributes['pro_newcod']);

        $rubros = rubros::where('cod_rubro', $array[0])->first();

        $this->codigoRubro = $rubros?->cod_rubro;
        $this->nombreRubro = $rubros?->rubro;

        $grupo = grupos::where('cod_rubro', $array[0])->where('cod_grupo', $array[1])->first();

        $this->codigoGrupo = $grupo?->cod_rg;
        $this->nombreGrupo = $grupo?->grupo;

        return $this;
    }

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
}
