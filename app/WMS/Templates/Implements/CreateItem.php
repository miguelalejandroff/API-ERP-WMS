<?php

namespace App\WMS\Templates\Implements;

use App\WMS\Templates\Abstracts\ItemService;
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
        return $model->enlacewms?->controlserie ?? parent::controlaSerie($model);
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
        return $model->enlacewms?->controlcantid ?? parent::controlaCantidad($model);
    }

    public function codTipo($model): string
    {
        return $model->enlacewms?->codtipo ?? parent::codTipo($model);
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
        return $model->enlacewms?->espickeable ?? parent::esPickeable($model);
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
