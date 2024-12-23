<?php

namespace App\WMS\Adapters\OrdenEntrada;

use App\Libs\WMS;
use App\WMS\Adapters\Admin\CreateProveedor;
use App\WMS\Contracts\Inbound\OrdenEntradaDetalleService;
use App\WMS\Contracts\Inbound\OrdenEntradaService;
use Illuminate\Support\Collection;

/**
 * Clase que representa la Guia de Recepcion - Ajustes.
 */
class Ajustes extends OrdenEntradaService
{
    /**
     * Retorna el código de depósito.
     *
     * @param object $model
     * @return string
     */
    protected function codDeposito($model): string
    {
        return $model->cmdetgui->first()->gui_bodori ?? 'N/A';
    }

    /**
     * Retorna el número de orden de entrada.
     *
     * @param object $model
     * @return string
     */
    public function nroOrdenEntrada($model): string
    {
        return $model->gui_numero ?? 'N/A';
    }

    /**
     * Retorna el código de tipo de ajuste.
     *
     * @param object $model
     * @return string
     */
    public function codTipo($model): string
    {
        return '2';
    }

    /**
     * Retorna la referencia número 1.
     *
     * @param object $model
     * @return string
     */
    public function nroReferencia($model): string
    {
        return $model->gui_numero ?? 'N/A';
    }

    /**
     * Retorna la referencia número 2.
     *
     * @param object $model
     * @return string
     */
    public function nroReferencia2($model): string
    {
        return $model->gui_tipgui ?? 'N/A';
    }

    /**
     * Retorna la fecha de emisión en formato 'Y-m-d'.
     *
     * @param object $model
     * @return string|null
     */
    public function fechaEmisionERP($model): ?string
    {
        return WMS::date($model->gui_fechag, 'Y-m-d') ?? null;
    }

    /**
     * Retorna el código de depósito origen.
     *
     * @param object $model
     * @return string|null
     */
    public function codDepositoOrigen($model): ?string
    {
        return $model->cmdetgui->first()->gui_bodori ?? null;
    }

    /**
     * Retorna la colección de detalles de la orden de entrada.
     *
     * @param object $model
     * @return \Illuminate\Support\Collection
     */
    public function ordenEntradaDetalle($model): Collection
    {
        return $model->cmdetgui->map(function ($detalleModel) {
            $detalle = new class($detalleModel) extends OrdenEntradaDetalleService
            {
                /**
                 * Retorna el código de depósito.
                 *
                 * @param object $model
                 * @return string
                 */
                protected function codDeposito($model): string
                {
                    return $model->gui_bodori ?? 'N/A';
                }

                /**
                 * Retorna el número de orden de entrada.
                 *
                 * @param object $model
                 * @return string
                 */
                public function nroOrdenEntrada($model): string
                {
                    return $model->gui_numero ?? 'N/A';
                }

                /**
                 * Retorna el código del ítem.
                 *
                 * @param object $model
                 * @return string
                 */
                public function codItem($model): string
                {
                    return $model->gui_produc ?? 'N/A';
                }

                /**
                 * Retorna la cantidad solicitada.
                 *
                 * @param object $model
                 * @return float
                 */
                public function cantidadSolicitada($model): float
                {
                    return (float)($model->gui_canrep ?? 0);
                }
            };

            return $detalle->get();
        });
    }
}
