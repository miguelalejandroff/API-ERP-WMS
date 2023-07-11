<?php

namespace App\WMS\Contracts;

use Illuminate\Http\JsonResponse;
use App\WMS\Build\AbstractBase;

abstract class ItemCodigoBarraService extends AbstractBase
{
    /**
     * 
     */
    public function codUnidadMedida($model): ?int
    {
        return 1;
    }

    /**
     * 
     */
    abstract protected function codItem($model): string;

    /**
     * 
     */
    abstract protected function codigoBarra($model): string;

    /**
     * 
     */
    abstract protected function alias($model): string;

    /**
     * 
     */
    public function factor($model): ?float
    {
        return 1;
    }

    /**
     * 
     */
    public function ancho($model): ?float
    {
        return 0;
    }

    /**
     * 
     */
    public function largo($model): ?float
    {
        return 0;
    }

    /**
     * 
     */
    public function alto($model): ?float
    {
        return 0;
    }

    /**
     * 
     */
    public function peso($model): ?float
    {
        return 0;
    }

    /**
     * 
     */
    public function volumen($model): ?float
    {
        return 0;
    }

    /**
     * 
     */
    public function secuencia($model): ?int
    {
        return 1;
    }

    public function getJson(): JsonResponse
    {
        return response()->json([
            'codOwner' => parent::codOwner()
        ]);
    }
}
