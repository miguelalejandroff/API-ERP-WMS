<?php

namespace App\WMS\Adapters\Admin;

use App\WMS\Contracts\Admin\ItemCodigoBarraService;

class CreateItemCodigoBarra extends ItemCodigoBarraService
{
    private const UNIDAD_MEDIDA_CODIGOS = [
        'UN' => 1,
        'CJ' => 3,
        'BO' => 16,
        'FR' => 17,
        'LA' => 18,
        'SC' => 19,
        'SO' => 20,
        'TA' => 21,
        'KG' => 22,
        'MT' => 23,
        'PK' => 24,
        'PA' => 25,
        'RO' => 26,
        'BT' => 27,
        'TO' => 29,
        'M2' => 30,
        'BA' => 31,
        'BI' => 32,
    ];

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

    public function codUnidadMedida($model): int
    {
        return self::UNIDAD_MEDIDA_CODIGOS[$model->cmproductos->pro_unimed] ?? 1;
    }

    public function ancho($model): ?float
    {
        return $this->toFloat($model->ancho);
    }

    public function largo($model): ?float
    {
        return $this->toFloat($model->largo);
    }

    public function alto($model): ?float
    {
        return $this->toFloat($model->alto);
    }

    public function peso($model): ?float
    {
        return $this->toFloat($model->peso);
    }

    public function volumen($model): ?float
    {
        return $this->toFloat($model->volumen);
    }

    /**
     * Helper para convertir valores a float.
     *
     * @param mixed $value
     * @return float|null
     */
    private function toFloat($value): ?float
    {
        return isset($value) ? (float)$value : null;
    }
}
