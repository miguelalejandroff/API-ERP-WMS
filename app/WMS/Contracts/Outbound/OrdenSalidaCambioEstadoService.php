<?php

namespace App\WMS\Contracts\Outbound;

use App\WMS\Build\AbstractBase;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

abstract class OrdenSalidaCambioEstadoService extends AbstractBase
{
    /**
     * Representa la Bodega a la cual se recibira la mercaderia 
     */
    abstract protected function codDeposito(): string;

    /**
     * Corresponde al numero unico del documento de ingreso
     */
    abstract protected function nroOrdenSalida(): string;

    /**
     * Numero opcional que puede referenciar al numero de orden de entrada
     */
    abstract protected function codEstado(): string;

    /**
     * Numero opcional que puede referenciar al numero de orden de entrada
     */
    abstract protected function observacion(): string;
    

    public function getJson(): JsonResponse
    {
        $ordenSalidaCambioEstado = parent::get();
        Log::info('Orden de Salida JSON:', [
            'codOwner' => parent::codOwner(),
            'codDeposito' => $ordenSalidaCambioEstado->codDeposito,
            'ordenSalidaCambioEstado' => $ordenSalidaCambioEstado,
        ]);
        return response()->json([
            'codOwner' => parent::codOwner(),
            'codDeposito' => $ordenSalidaCambioEstado->codDeposito,
            'ordenSalidaCambioEstado' => $ordenSalidaCambioEstado,
        ]);
    }
}
