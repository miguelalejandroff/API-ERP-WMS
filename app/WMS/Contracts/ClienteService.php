<?php

namespace App\WMS\Contracts;

use Illuminate\Http\JsonResponse;
use App\WMS\Build\AbstractBase;

abstract class ClienteService extends AbstractBase
{

    protected $fields = [
        'codCliente',
        'rutCliente',
        'nomCliente',
        'razonSocial',
        'nomCorto',
        'giroCliente',
        'direccion',
        'comuna',
        'ciudad',
        'pais',
        'localidad',
        'telefono',
        'eMail',
        'contacto',
        'fillRate',
        'codTipo',
        'b2B',
    ];

    /**
     * Representa el CD, Site o Bodega a la cual se recibira la mercaderia
     */
    abstract protected function codCliente($model): string;

    /**
     * Corresponde al numero unico del documento de ingreso
     */
    abstract protected function rutCliente($model): string;

    /**
     * Corresponde al numero unico del documento de ingreso
     */
    abstract protected function nomCliente($model): string;

    /**
     * Corresponde al numero unico del documento de ingreso
     */
    public function razonSocial($model): ?string
    {
        return null;
    }

    /**
     * Corresponde al numero unico del documento de ingreso
     */
    public function nomCorto($model): ?string
    {
        return null;
    }

    /**
     * Corresponde al numero unico del documento de ingreso
     */
    public function giroCliente($model): ?string
    {
        return null;
    }
    /**
     * Corresponde al numero unico del documento de ingreso
     */
    public function direccion($model): ?string
    {
        return null;
    }

    /**
     * Corresponde al numero unico del documento de ingreso
     */
    public function comuna($model): ?string
    {
        return null;
    }

    /**
     * Corresponde al numero unico del documento de ingreso
     */
    public function ciudad($model): ?string
    {
        return null;
    }

    /**
     * Corresponde al numero unico del documento de ingreso
     */
    public function pais($model): string
    {
        return "CHILE";
    }

    /**
     * Corresponde al numero unico del documento de ingreso
     */
    public function localidad($model): ?string
    {
        return null;
    }

    /**
     * Corresponde al numero unico del documento de ingreso
     */
    public function telefono($model): ?string
    {
        return null;
    }

    /**
     * Corresponde al numero unico del documento de ingreso
     */
    public function eMail($model): ?string
    {
        return null;
    }

    /**
     * Corresponde al numero unico del documento de ingreso
     */
    public function contacto($model): ?string
    {
        return null;
    }

    /**
     * Corresponde al numero unico del documento de ingreso
     */
    public function fillRate($model): int
    {
        return 0;
    }
    /**
     * Corresponde al numero unico del documento de ingreso
     */
    public function codTipo($model): int
    {
        return 1;
    }

    /**
     * Corresponde al numero unico del documento de ingreso
     */
    public function b2B($model): ?string
    {
        return null;
    }

    public function getJson(): JsonResponse
    {
        return response()->json([
            'codOwner' => parent::codOwner()
        ]);
    }
}
