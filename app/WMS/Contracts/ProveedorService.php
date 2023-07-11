<?php

namespace App\WMS\Contracts;

use App\WMS\Build\AbstractBase;
use Illuminate\Http\JsonResponse;

abstract class ProveedorService extends AbstractBase
{

    /**
     * Representa el CD, Site o Bodega a la cual se recibira la mercaderia
     */
    abstract protected function codProveedor($model): string;

    /**
     * Corresponde al numero unico del documento de ingreso
     */
    abstract protected function rutProveedor($model): string;

    /**
     * Corresponde al numero unico del documento de ingreso
     */
    abstract protected function nomProveedor($model): string;

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
    public function giroProveedor($model): ?string
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

    public function getJson(): JsonResponse
    {
        return response()->json([
            'codOwner' => parent::codOwner()
        ]);
    }
}
