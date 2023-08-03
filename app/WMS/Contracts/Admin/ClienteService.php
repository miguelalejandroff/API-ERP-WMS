<?php

namespace App\WMS\Contracts\Admin;

use Illuminate\Http\JsonResponse;
use App\WMS\Build\AbstractBase;

abstract class ClienteService extends AbstractBase
{

    /**
     * Codigo unico de cliente en el ERP
     */
    abstract protected function codCliente($model): string;

    /**
     * Rut unico de cliente en el ERP
     */
    abstract protected function rutCliente($model): string;

    /**
     * Nombre del cliente
     */
    abstract protected function nomCliente($model): string;

    /**
     * Razon social del cliente
     */
    public function razonSocial($model): ?string
    {
        return null;
    }

    /**
     * Nombre corto del cliente
     */
    public function nomCorto($model): ?string
    {
        return null;
    }

    /**
     * Giro del cliente
     */
    public function giroCliente($model): ?string
    {
        return null;
    }

    /**
     * Dirccion principal o casa matriz del cliente
     */
    public function direccion($model): ?string
    {
        return null;
    }

    /**
     * Comuna de direccion del cliente
     */
    public function comuna($model): ?string
    {
        return null;
    }

    /**
     * Ciudad de direccion del cliente
     */
    public function ciudad($model): ?string
    {
        return null;
    }

    /**
     * Pais de direccion del cliente
     */
    public function pais($model): string
    {
        return "CHILE";
    }

    /**
     * Localidad de direccion del cliente
     */
    public function localidad($model): ?string
    {
        return null;
    }

    /**
     * Telefono del cliente
     */
    public function telefono($model): ?string
    {
        return null;
    }

    /**
     * Correo electronico del cliente
     */
    public function eMail($model): ?string
    {
        return null;
    }

    /**
     * Nombre de Contacto de Cliente
     */
    public function contacto($model): ?string
    {
        return null;
    }

    /**
     * % de Fill Rate al cliente
     */
    public function fillRate($model): int
    {
        return 0;
    }
    /**
     * Tipo de cliente
     */
    public function codTipo($model): int
    {
        return 1;
    }

    /**
     * Indica si cliente es de tipo B2B (Grandes Tiendas) "S" o "N"
     */
    public function b2B($model): ?string
    {
        return null;
    }

    public function getJson(): JsonResponse
    {
        return response()->json([
            'codOwner' => parent::codOwner(),
            'cliente' => parent::get()
        ]);
    }
}
