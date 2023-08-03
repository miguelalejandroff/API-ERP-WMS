<?php

namespace App\WMS\Adapters\Admin;

use App\WMS\Contracts\Admin\ClienteService;

class CreateCliente extends ClienteService
{
    protected function codCliente($model): string
    {
        return  $model->aux_claves;
    }
    protected function rutCliente($model): string
    {
        return  "{$model->aux_numrut}-{$model->aux_digrut}";
    }
    protected function nomCliente($model): string
    {
        return $model->aux_nombre;
    }
    public function giroCliente($model): ?string
    {
        return  $model->aux_nacion;
    }
    public function direccion($model): ?string
    {
        return  $model->aux_direcc;
    }
    public function comuna($model): ?string
    {
        return  $model->comuna;
    }
    public function ciudad($model): ?string
    {
        return  $model->ciudad;
    }
    public function telefono($model): ?string
    {
        return $model->aux_telefo;
    }
}
