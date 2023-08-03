<?php

namespace App\WMS\Adapters\Admin;

use App\WMS\Contracts\Admin\ProveedorService;

class CreateProveedor extends ProveedorService
{
    protected function codProveedor($model): string
    {
        return $model->aux_claves;
    }
    protected function rutProveedor($model): string
    {
        return "{$model->aux_numrut}-{$model->aux_digrut}";
    }
    protected function nomProveedor($model): string
    {
        return $model->aux_nombre;
    }
    public function giroProveedor($model): ?string
    {
        return $model->aux_nacion;
    }
    public function direccion($model): ?string
    {
        return $model->aux_direcc;
    }
    public function comuna($model): ?string
    {
        return $model->comuna;
    }
    public function ciudad($model): ?string
    {
        return $model->ciudad;
    }
    public function telefono($model): ?string
    {
        return $model->aux_telefo;
    }
}
