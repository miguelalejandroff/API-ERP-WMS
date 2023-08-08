<?php

namespace App\WMS\Contracts\Admin;

use Illuminate\Http\JsonResponse;
use App\WMS\Build\AbstractBase;

abstract class ItemClaseService extends AbstractBase
{

    /**
     * Codigo de la clasificacion
     */
    abstract protected function codItemClase($model): string;

    /**
     * Descripcion de la clasificacion
     */
    abstract protected function nomItemClase($model): string;

    /**
     * Alias o nombre de la clasificacion, por ejemplo: FAMILIA, SUBFAMILIA, RUBRO, ETC
     */
    abstract protected function alias($model): string;

    public function getJson(): JsonResponse
    {
        $itemClase = parent::get();
        $alias = $itemClase->alias;
        $aliasMappings = [
            'RUBRO' => 1,
            'SUBRUBRO' => 2,
        ];
        return response()->json([
            'codOwner' => parent::codOwner(),
            'codTipoItemClase' => $aliasMappings[$alias] ?? null, // 1 para rubro y 2 para subRubro
            'itemClase' => [$itemClase]
        ]);
    }
}
