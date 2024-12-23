<?php

namespace App\WMS\Adapters\OrdenEntrada;

use App\Libs\WMS;
use App\WMS\Adapters\Admin\CreateProveedor;
use App\WMS\Contracts\Inbound\OrdenEntradaDetalleService;
use App\WMS\Contracts\Inbound\OrdenEntradaService;
use Illuminate\Support\Collection;

class GuiaDespacho extends OrdenEntradaService
{
    /**
     * Devuelve el código del depósito.
     *
     * @param object $model
     * @return string
     */
    protected function codDeposito(object $model): string
    {
        return $model->cmdetgui->first()?->gui_boddes ?? '';
    }

    /**
     * Devuelve el número de orden de entrada.
     *
     * @param object $model
     * @return string
     */
    public function nroOrdenEntrada(object $model): string
    {
        return $model->gui_numero;
    }

    /**
     * Devuelve el código de tipo basado en gui_tipgui.
     *
     * @param object $model
     * @return string
     */
    public function codTipo(object $model): string
    {
        return match ($model->gui_tipgui) {
            '05' => '5',
            '06' => '6',
            '11' => '12',
            '48' => '48',
            '39' => '39',
            '21' => '21',
            default => '',
        };
    }

    /**
     * Devuelve el número de referencia principal.
     *
     * @param object $model
     * @return string
     */
    public function nroReferencia(object $model): string
    {
        return $model->gui_numero;
    }

    /**
     * Devuelve el número de referencia secundaria.
     *
     * @param object $model
     * @return string
     */
    public function nroReferencia2(object $model): string
    {
        return $model->gui_tipgui;
    }

    /**
     * Devuelve el código del proveedor.
     *
     * @param object $model
     * @return string
     */
    public function codProveedor(object $model): string
    {
        return $model->gui_subcta;
    }

    /**
     * Devuelve el código de sucursal.
     *
     * @param object $model
     * @return string|null
     */
    public function codSucursal(object $model): ?string
    {
        return $model->gui_sucori;
    }

    /**
     * Devuelve la fecha de emisión en formato ERP.
     *
     * @param object $model
     * @return string|null
     */
    public function fechaEmisionERP(object $model): ?string
    {
        return WMS::date($model->gui_fechag, 'Y-m-d');
    }

    /**
     * Procesa los detalles de la orden de entrada.
     *
     * @param object $model
     * @return Collection
     */
    public function ordenEntradaDetalle(object $model): Collection
    {
        return $model->cmdetgui->map(function ($detalleModel) {
            return (new class($detalleModel) extends OrdenEntradaDetalleService {
                /**
                 * Devuelve el código del depósito.
                 *
                 * @param object $model
                 * @return string
                 */
                protected function codDeposito(object $model): string
                {
                    return $model->gui_boddes;
                }

                /**
                 * Devuelve el número de orden de entrada.
                 *
                 * @param object $model
                 * @return string
                 */
                public function nroOrdenEntrada(object $model): string
                {
                    return $model->gui_numero;
                }

                /**
                 * Devuelve el código del ítem.
                 *
                 * @param object $model
                 * @return string
                 */
                public function codItem(object $model): string
                {
                    return $model->gui_produc;
                }

                /**
                 * Devuelve la cantidad solicitada.
                 *
                 * @param object $model
                 * @return float
                 */
                public function cantidadSolicitada(object $model): float
                {
                    return (float) $model->gui_canrep;
                }
            })->get();
        });
    }

    /**
     * Devuelve los datos del proveedor.
     *
     * @param object $model
     * @return array
     */
    public function proveedor(object $model): array
    {
        return (new CreateProveedor($model->cmclientes))->get();
    }
}
