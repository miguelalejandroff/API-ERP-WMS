<?php

namespace App\WMS\Adapters\Admin;

use App\WMS\Contracts\Admin\ItemService;
use Illuminate\Support\Collection;

class CreateItem extends ItemService
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

    private const DEFAULTS = [
        'controlaSerie' => 'N',
        'controlaCantidad' => 'S',
        'esPickeable' => 'S',
        'codTipo' => 1
    ];

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
        return $model->pro_newcod ?? $this->codItem($model);
    }

    public function nomAlternativo($model): string
    {
        return $this->nomItem($model);
    }

    public function controlaSerie($model): string
    {
        return self::DEFAULTS['controlaSerie'];
    }

    public function controlaCantidad($model): string
    {
        return self::DEFAULTS['controlaCantidad'];
    }

    public function codUnidadMedida($model): int
    {
        return self::UNIDAD_MEDIDA_CODIGOS[$model->pro_unimed] ?? 1;
    }

    public function codTipo($model): string
    {
        return self::DEFAULTS['codTipo'];
    }

    public function esPickeable($model): string
    {
        return self::DEFAULTS['esPickeable'];
    }

    private function getControlaValue($model, string $property, $default = 'N'): string
    {
        return $model->enlacewms->{$property} ?? $default;
    }

    public function controlaLote($model): string
    {
        return $this->getControlaValue($model, 'controllote');
    }

    public function controlaExpiracion($model): string
    {
        return $this->getControlaValue($model, 'controlexpira');
    }

    public function controlaFabricacion($model): string
    {
        return $this->getControlaValue($model, 'controlfabrica');
    }

    public function controlaVAS($model): string
    {
        return $this->getControlaValue($model, 'controlvas');
    }

    public function inspeccion($model): string
    {
        return $this->getControlaValue($model, 'inspeccion', parent::inspeccion($model));
    }

    public function cuarentena($model): string
    {
        return $this->getControlaValue($model, 'cuarentena', parent::cuarentena($model));
    }

    public function crossDocking($model): string
    {
        return $this->getControlaValue($model, 'crossdocking', parent::crossDocking($model));
    }

    public function codItemClase1($model): ?string
    {
        return $model->productoClase->codigoRubro ?? null;
    }

    public function nomItemClase1($model): ?string
    {
        return $model->productoClase->nombreRubro ?? null;
    }

    public function codItemClase2($model): ?string
    {
        return $model->productoClase->codigoGrupo ?? null;
    }

    public function nomItemClase2($model): ?string
    {
        return $model->productoClase->nombreGrupo ?? null;
    }

    public function itemCodigoBarra($model): Collection
    {
        return $model->wmscodigobarra
            ->filter(fn($barcode) => !empty($barcode->codigo_barra) && !empty($barcode->tipo_codigo))
            ->map(fn($barcode) => (new CreateItemCodigoBarra($barcode))->get());
    }
}
