<?php

namespace App\WMS\Adapters\Admin;

use App\WMS\Contracts\Admin\ItemService;
use Illuminate\Support\Collection;

class CreateItem extends ItemService
{

    protected function codItem($model): string
    {
        return $model->pro_codigo;
    }

    protected function nomItem($model): string
    {
        return $model->pro_descri;
    }

    public function codItemAlternativo($model): string
    {
        return $model->pro_newcod;
    }

    public function nomAlternativo($model): string
    {
        return $model->pro_descri;
    }

    public function controlaLote($model): string
    {
        return $model->enlacewms?->controllote ?? parent::controlaLote($model);
    }

    public function controlaSerie($model): string
    {
        return "N";
    }

    public function controlaExpiracion($model): string
    {
        return $model->enlacewms?->controlexpira ?? parent::controlaExpiracion($model);
    }

    public function controlaFabricacion($model): string
    {
        return $model->enlacewms?->controlfabrica ?? parent::controlaFabricacion($model);
    }

    public function controlaVAS($model): string
    {
        return $model->enlacewms?->controlvas ?? parent::controlaVAS($model);
    }

    public function controlaCantidad($model): string
    {
        return "S";
    }

    public function codUnidadMedida($model) : int
    {
        switch ($model->pro_unimed) { 
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

    public function codTipo($model): string
    {
        return 1;
    }

    public function marca($model): ?string
    {
        return $model->enlacewms?->marca;
    }

    public function origen($model): string
    {
        return $model->pro_impnac;
    }

    public function esPickeable($model): string
    {
        return "S";
    }

    public function inspeccion($model): string
    {
        return $model->enlacewms?->inspeccion ?? parent::inspeccion($model);
    }

    public function cuarentena($model): string
    {
        return $model->enlacewms?->cuarentena ?? parent::cuarentena($model);
    }

    public function crossDocking($model): string
    {
        return $model->enlacewms?->crossdocking ?? parent::crossDocking($model);
    }

    public function codItemClase1($model): ?string
    {
        return $model->productoClase->codigoRubro;
    }

    public function nomItemClase1($model): ?string
    {
        return $model->productoClase->nombreRubro;
    }

    public function codItemClase2($model): ?string
    {
        return $model->productoClase->codigoGrupo;
    }

    public function nomItemClase2($model): ?string
    {
        return $model->productoClase->nombreGrupo;
    }
    public function itemCodigoBarra($model): Collection
    {
        return  $model->wmscodigobarra->map(function ($model) {
            if (!empty($model->codigo_barra) && !empty($model->tipo_codigo)) {
                return (new CreateItemCodigoBarra($model))->get();
            }
        });
    }
}
