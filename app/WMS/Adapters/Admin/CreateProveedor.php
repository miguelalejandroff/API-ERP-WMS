<?php

namespace App\WMS\Adapters\Admin;

use App\WMS\Contracts\Admin\ProveedorService;

class CreateProveedor extends ProveedorService
{
    /**
     * Retorna el código del proveedor.
     *
     * @param object $model
     * @return string
     */
    protected function codProveedor($model): string
    {
        return $model->aux_claves ?? 'N/A';
    }

    /**
     * Retorna el RUT del proveedor en formato 'num-digito'.
     *
     * @param object $model
     * @return string
     */
    protected function rutProveedor($model): string
    {
        return $this->concatenarRut($model->aux_numrut, $model->aux_digrut);
    }

    /**
     * Retorna el nombre del proveedor.
     *
     * @param object $model
     * @return string
     */
    protected function nomProveedor($model): string
    {
        return $model->aux_nombre ?? 'Sin Nombre';
    }

    /**
     * Retorna el giro del proveedor.
     *
     * @param object $model
     * @return string|null
     */
    public function giroProveedor($model): ?string
    {
        return $model->aux_nacion ?? null;
    }

    /**
     * Retorna la dirección del proveedor.
     *
     * @param object $model
     * @return string|null
     */
    public function direccion($model): ?string
    {
        return $model->aux_direcc ?? null;
    }

    /**
     * Retorna la comuna del proveedor.
     *
     * @param object $model
     * @return string|null
     */
    public function comuna($model): ?string
    {
        return $model->comuna ?? null;
    }

    /**
     * Retorna la ciudad del proveedor.
     *
     * @param object $model
     * @return string|null
     */
    public function ciudad($model): ?string
    {
        return $model->ciudad ?? null;
    }

    /**
     * Retorna el teléfono del proveedor.
     *
     * @param object $model
     * @return string|null
     */
    public function telefono($model): ?string
    {
        return $model->aux_telefo ?? null;
    }

    /**
     * Helper para concatenar RUT.
     *
     * @param string|null $num
     * @param string|null $dig
     * @return string
     */
    private function concatenarRut(?string $num, ?string $dig): string
    {
        return isset($num, $dig) ? "{$num}-{$dig}" : 'N/A';
    }
}
