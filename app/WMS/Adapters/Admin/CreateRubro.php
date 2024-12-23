<?php

namespace App\WMS\Adapters\Admin;

use App\WMS\Contracts\Admin\ItemClaseService;

class CreateRubro extends ItemClaseService
{
    /**
     * Retorna el código del rubro.
     *
     * @param object $model
     * @return string
     */
    protected function codItemClase($model): string
    {
        return $model->cod_rubro ?? 'N/A';
    }

    /**
     * Retorna el nombre del rubro.
     *
     * @param object $model
     * @return string
     */
    protected function nomItemClase($model): string
    {
        return $model->rubro ?? 'Sin Nombre';
    }

    /**
     * Retorna el alias para la clase.
     *
     * @param object $model
     * @return string
     */
    protected function alias($model): string
    {
        return "RUBRO";
    }
}
