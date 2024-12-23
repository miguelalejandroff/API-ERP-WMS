<?php

namespace App\ERP\Handler;

use App\Models\cmsalbod;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use App\Models\vpparsis;

class SaldoBodegaTransitoHandler
{
    protected $periodoActual = [
        "bod_salene", "bod_salfeb", "bod_salmar", "bod_salabr", "bod_salmay", "bod_saljun",
        "bod_saljul", "bod_salago", "bod_salsep", "bod_saloct", "bod_salnov", "bod_saldic",
        "bod_salen2", "bod_salfe2", "bod_salma2", "bod_salab2", "bod_salmy2", "bod_salju2"
    ];

    protected $periodoAnterior = ["bod_salen2", "bod_salfe2", "bod_salma2", "bod_salab2", "bod_salmy2", "bod_salju2"];

    public function handle($context)
    {
        Log::info('SaldoBodegaTransitoHandler iniciado');

        $context->guiaCompra->iterarDetalle(function ($detalle) {
            try {
                $producto = $this->getOrCreateBodega($detalle->gui_produc, $detalle->gui_boddes);
                $this->actualizarModelo($producto, $detalle->gui_canord);
            } catch (Exception $e) {
                Log::error('Error en el manejo del producto', ['error' => $e->getMessage()]);
            }
        });

        Log::info('SaldoBodegaTransitoHandler finalizado');
    }

    /**
     * Recupera o crea un modelo de Bodega.
     *
     * @param string $codigoProducto Código del producto.
     * @param string $bodega ID de la bodega.
     * @return cmsalbod
     */
    private function getOrCreateBodega($codigoProducto, $bodega)
    {
        $producto = cmsalbod::where('bod_produc', $codigoProducto)
            ->where('bod_bodega', $bodega)
            ->where('bod_ano', Carbon::now()->year)
            ->first();

        if (!$producto) {
            Log::info("Producto no encontrado, creando nuevo registro", ['producto' => $codigoProducto]);
            return $this->createBodega($codigoProducto, $bodega);
        }

        return $producto;
    }

    /**
     * Actualiza el modelo con la cantidad solicitada.
     *
     * @param cmsalbod $producto
     * @param int $cantidadSolicitada
     */
    private function actualizarModelo($producto, $cantidadSolicitada)
    {
        $now = Carbon::now();
        $periodo = $this->getPeriodo($now);
        $campoPeriodo = $periodo === 'actual' ? $this->getPeriodoActual($now->month) : $this->getPeriodoAnterior($now->month);

        Log::info("Actualizando saldo de bodega", ['campoPeriodo' => $campoPeriodo, 'cantidad' => $cantidadSolicitada]);

        $producto->bod_stockb += $cantidadSolicitada;
        $producto->bod_stolog += $cantidadSolicitada;
        $producto->$campoPeriodo += $cantidadSolicitada;

        $this->replicarEnPeriodos($producto, $campoPeriodo, $cantidadSolicitada);
        $producto->save();

        Log::info('Producto actualizado correctamente', ['producto' => $producto->toArray()]);
    }

    /**
     * Replica el valor en los periodos inferiores.
     *
     * @param cmsalbod $producto
     * @param string $campoPeriodo
     * @param int $cantidad
     */
    private function replicarEnPeriodos($producto, $campoPeriodo, $cantidad)
    {
        $periodos = in_array($campoPeriodo, $this->periodoAnterior) ? $this->periodoAnterior : $this->periodoActual;

        $indice = array_search($campoPeriodo, $periodos);

        for ($i = $indice + 1; $i < count($periodos); $i++) {
            $producto->{$periodos[$i]} += $cantidad;
        }
    }

    /**
     * Determina si el periodo es actual o anterior.
     *
     * @param Carbon $now
     * @return string
     */
    private function getPeriodo(Carbon $now)
    {
        $yearParSis = Carbon::createFromFormat('Y-m-d', vpparsis::first()->par_fechas)->year;
        return $now->year === $yearParSis ? 'actual' : 'anterior';
    }

    /**
     * Devuelve el campo de periodo actual correspondiente al mes.
     *
     * @param int $month
     * @return string
     */
    private function getPeriodoActual($month)
    {
        return $this->periodoActual[$month - 1];
    }

    /**
     * Devuelve el campo de periodo anterior correspondiente al mes.
     *
     * @param int $month
     * @return string
     */
    private function getPeriodoAnterior($month)
    {
        return $this->periodoAnterior[$month - 1];
    }

    /**
     * Crea un nuevo registro en la bodega.
     *
     * @param string $codigoProducto
     * @param string $bodega
     * @return cmsalbod
     */
    private function createBodega($codigoProducto, $bodega)
    {
        $saldoBodega = new cmsalbod();
        $saldoBodega->bod_ano = Carbon::now()->year;
        $saldoBodega->bod_produc = $codigoProducto;
        $saldoBodega->bod_bodega = $bodega;
        $saldoBodega->bod_stockb = 0;
        $saldoBodega->bod_stolog = 0;

        foreach (array_merge($this->periodoActual, $this->periodoAnterior) as $campo) {
            $saldoBodega->$campo = 0;
        }

        $saldoBodega->save();
        return $saldoBodega;
    }
}
