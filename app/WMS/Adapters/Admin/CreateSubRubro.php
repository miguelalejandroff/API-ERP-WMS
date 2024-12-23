<?php

namespace App\WMS\Adapters\Admin;

use App\WMS\Contracts\Admin\ItemClaseService;

class CreateSubRubro extends ItemClaseService
{
    /**
     * Retorna el código del subrubro.
     *
     * @param object $model
     * @return string
     */
    protected function codItemClase($model): string
    {
        return $model->cod_rg ?? 'N/A';
    }

    /**
     * Retorna el nombre del subrubro.
     *
     * @param object $model
     * @return string
     */
    protected function nomItemClase($model): string
    {
        return $model->grupo ?? 'Sin Nombre';
    }

    /**
     * Retorna el alias asociado al subrubro.
     *
     * @param object $model
     * @return string
     */
    protected function alias($model): string
    {
        return "SUBRUBRO";
    }
}
