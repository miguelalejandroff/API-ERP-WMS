<?php

namespace App\WMS\Adapters\Admin;

use App\WMS\Contracts\Admin\ItemCodigoBarraService;

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
        return "ERP";
    }
    public function codUnidadMedida($model) : int
    {
        switch ($model->cmproductos->pro_unimed) { 
            case 'UN':
                return 1;
            case 'CJ':
                return 3;
            case 'BO':
                return 16;
            case 'FR':
                return 17;
            case 'LA':
                return 18;
            case 'SC':
                return 19;
            case 'SO':
                return 20;
            case 'TA':
                return 21;
            case 'KG':
                return 22;
            case 'MT':
                return 23;
            case 'PK':
                return 24;
            case 'PA':
                return 25;
            case 'RO':
                return 26;
            case 'BT':
                return 27;
            case 'TA':
                return 28;
            case 'TO':
                return 29;
            case 'M2':
                return 30;
            case 'BA':
                return 31;
            case 'BI':
                return 32;
            default:
                return 1;
        }
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
