<?php

namespace App\WMS\Adapters\Admin;

use App\WMS\Contracts\Admin\ItemClaseService;

class CreateSubRubro extends ItemClaseService
{

    protected function codItemClase($model): string
    {
        return $model->cod_rg;
    }

    protected function nomItemClase($model): string
    {
        return $model->grupo;
    }

    protected function alias($model): string
    {
        return "SUBRUBRO";
    }
}
