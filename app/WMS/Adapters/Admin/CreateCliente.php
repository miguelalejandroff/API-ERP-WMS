<?php

namespace App\WMS\Adapters\Admin;

use App\WMS\Contracts\Admin\ClienteService;

class CreateCliente extends ClienteService
{
    /**
     * Obtiene el código del cliente.
     *
     * @param object $model
     * @return string
     */
    protected function codCliente($model): string
    {
        return $model->aux_claves ?? 'N/A';
    }

    /**
     * Formatea y devuelve el RUT del cliente.
     *
     * @param object $model
     * @return string
     */
    protected function rutCliente($model): string
    {
        return isset($model->aux_numrut, $model->aux_digrut)
            ? "{$model->aux_numrut}-{$model->aux_digrut}"
            : 'N/A';
    }

    /**
     * Obtiene el nombre del cliente.
     *
     * @param object $model
     * @return string
     */
    protected function nomCliente($model): string
    {
        return $model->aux_nombre ?? 'Sin Nombre';
    }

    /**
     * Obtiene el giro del cliente.
     *
     * @param object $model
     * @return string|null
     */
    public function giroCliente($model): ?string
    {
        return $model->aux_nacion ?? null;
    }

    /**
     * Obtiene la dirección del cliente.
     *
     * @param object $model
     * @return string|null
     */
    public function direccion($model): ?string
    {
        return $model->aux_direcc ?? null;
    }

    /**
     * Obtiene la comuna del cliente.
     *
     * @param object $model
     * @return string|null
     */
    public function comuna($model): ?string
    {
        return $model->comuna ?? null;
    }

    /**
     * Obtiene la ciudad del cliente.
     *
     * @param object $model
     * @return string|null
     */
    public function ciudad($model): ?string
    {
        return $model->ciudad ?? null;
    }

    /**
     * Obtiene el teléfono del cliente.
     *
     * @param object $model
     * @return string|null
     */
    public function telefono($model): ?string
    {
        return $model->aux_telefo ?? null;
    }

    /**
     * Exporta los datos del cliente como un array.
     *
     * @param object $model
     * @return array
     */
    public function toArray($model): array
    {
        return [
            'codigo_cliente' => $this->codCliente($model),
            'rut_cliente' => $this->rutCliente($model),
            'nombre_cliente' => $this->nomCliente($model),
            'giro_cliente' => $this->giroCliente($model),
            'direccion' => $this->direccion($model),
            'comuna' => $this->comuna($model),
            'ciudad' => $this->ciudad($model),
            'telefono' => $this->telefono($model),
        ];
    }
}
