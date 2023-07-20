<?php

namespace App\WMS\Adapters;

use App\WMS\Contracts\ItemClaseService;

class CreateRubro extends ItemClaseService
{

    protected function codItemClase($model): string
    {
        return $model->cod_rubro;
    }

    protected function nomItemClase($model): string
    {
        return $model->rubro;
    }

    protected function alias($model): string
    {
        return "RUBRO";
    }
}
