<?php

namespace App\WMS\Adapters;

use App\Models\cmproductos;
use App\WMS\Build\Adapter;
use App\WMS\Contracts\WMSItemCodigoBarraService;
use App\WMS\Templates\ItemCodigoBarra;

class CreateItemCodigoBarra extends Adapter implements WMSItemCodigoBarraService
{
    public function makeItemCodigoBarra(cmproductos $model): array
    {
        $arr = [];
        if (!is_null($model->wmscodigobarra)) {
            foreach ($model->wmscodigobarra as $key => $row) {
                if (!empty($row->codigo_barra) && empty($row->tipo_codigo)) {
                    $arr[] = new ItemCodigoBarra(
                        codUnidadMedida: null,
                        codItem: $model->pro_codigo,
                        codigoBarra: $row->codigo_barra,
                        alias: "UN",
                        factor: 1,
                        ancho: $row->ancho,
                        largo: $row->largo,
                        alto: $row->alto,
                        peso: $row->peso,
                        volumen: $row->volumen,
                        secuencia: $key,
                    );
                }
            }
        }
        return $arr;
    }
}
