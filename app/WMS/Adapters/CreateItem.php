<?php

namespace App\WMS\Adapters;

use App\Models\cmproductos;
use App\WMS\Build\Adapter;
use App\WMS\Contracts\WMSItemCodigoBarraService;
use App\WMS\Contracts\WMSItemService;
use App\WMS\Templates\Item;

class CreateItem extends Adapter implements WMSItemService
{
    public function makeItem(cmproductos $model, WMSItemCodigoBarraService $codigoBarra): array
    {
        return [new Item(
            codItem: $model->pro_codigo,
            nomItem: $model->pro_descri,
            codUnidadMedida: null,
            codItemAlternativo: $model->pro_newcod,
            nomAlternativo: $model->pro_descri,
            controlaLote: $model->enlacewms?->controllote,
            controlaSerie: $model->enlacewms?->controlserie,
            controlaExpiracion: $model->enlacewms?->controlexpira,
            controlaFabricacion: $model->enlacewms?->controlfabrica,
            controlaVAS: $model->enlacewms?->controlvas,
            controlaCantidad: $model->enlacewms?->controlcantid,
            codTipo: $model->enlacewms?->codtipo,
            marca: $model->enlacewms?->marca,
            origen: $model->pro_impnac,
            esPickeable: $model->enlacewms?->espickeable,
            inspeccion: $model->enlacewms?->inspeccion,
            cuarentena: $model->enlacewms?->cuarentena,
            crossDocking: $model->enlacewms?->crossdocking,
            codItemClase1: $model->enlacewms?->coditemclase1,
            codItemClase2: $model->enlacewms?->coditemclase2,
            codItemClase3: null,
            codItemClase4: null,
            codItemClase5: null,
            codItemClase6: null,
            codItemClase7: null,
            codItemClase8: null,
            itemCodigoBarra: $codigoBarra->get($model)['itemCodigoBarra'],
        )];
    }
}
