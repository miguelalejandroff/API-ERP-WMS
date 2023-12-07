<?php

namespace App\WMS\Contracts\Admin;

use Illuminate\Http\JsonResponse;
use App\WMS\Build\AbstractBase;
use Illuminate\Support\Collection;

abstract class ItemCodigoBarraService extends AbstractBase
{

    private static $lineaActual = 0;

    public function __construct($model)
    {
        parent::__construct($model);

        self::$lineaActual++;
    }
    /**
     * Codigo de la unidad de medida asociada al codigo de barras
     */
    public function codUnidadMedida($model): ?int
    {
        return 1;
    }

    /**
     * Codigo que identifica el item (SKU)
     */
    abstract protected function codItem($model): string;

    /**
     * codigo de barra del item (SKU), puede ser GTIN13, GTIN14, etc
     */
    abstract protected function codigoBarra($model): string;

    /**
     * Alias del codigo de barra, sino hay alias, se informa el mismo codigo de barras
     */
    abstract protected function alias($model): string;

    /**
     * Multiplo o factor de conversion del codigo de barra; por ejemplo: 1
     */
    public function factor($model): ?float
    {
        return 1;
    }

    /**
     * Maestro logistico: Indicador del ancho del producto que representa el codigo de barra
     */
    public function ancho($model): ?float
    {
        return 0;
    }

    /**
     * Maestro logistico: Indicador del largo del producto que representa el codigo de barra
     */
    public function largo($model): ?float
    {
        return 0;
    }

    /**
     * Maestro logistico: Indicador del alto del producto que representa el codigo de barra
     */
    public function alto($model): ?float
    {
        return 0;
    }

    /**
     * Maestro logistico: Indicador del peso del producto que representa el codigo de barra
     */
    public function peso($model): ?float
    {
        return 0;
    }

    /**
     * Maestro logistico: Indicador del volumen del producto que representa el codigo de barra
     */
    public function volumen($model): ?float
    {
        return 0;
    }

    /**
     * Secuencia o prioridad en los codigos de barra del item
     */
    public function secuencia($model): ?int
    {
        return self::$lineaActual;
    }

    public function getJson(): JsonResponse
    {
        $itemCodigoBarra = parent::get();
        return response()->json([
            'codOwner' => parent::codOwner(),
            'itemCodigoBarra' => [$itemCodigoBarra]
        ]);
    }
}
