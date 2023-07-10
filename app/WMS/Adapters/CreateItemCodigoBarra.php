<?php

namespace App\WMS\Adapters;

use App\WMS\Contracts\ItemCodigoBarraService;

class CreateItemCodigoBarra extends ItemCodigoBarraService
{
    protected function codItem($model): string
    {
        return $model->codigo_antig;
    }
    protected function codigoBarra($model): string
    {
        return $model->codigo_barra;
    }
    protected function alias($model): string
    {
        return "UN";
    }
    public function ancho($model): ?float
    {
        return (float)$model->ancho;
    }
    public function largo($model): ?float
    {
        return (float)$model->largo;
    }
    public function alto($model): ?float
    {
        return (float)$model->alto;
    }
    public function peso($model): ?float
    {
        return (float)$model->peso;
    }
    public function volumen($model): ?float
    {
        return (float)$model->volumen;
    }
}
