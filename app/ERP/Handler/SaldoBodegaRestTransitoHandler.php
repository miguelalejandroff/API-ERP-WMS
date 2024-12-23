<?php

namespace App\ERP\Handler;

use App\Models\cmsalbod;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use App\Models\vpparsis;

class SaldoBodegaRestTransitoHandler
{
    protected $periodoActual = [
        "bod_salene", "bod_salfeb", "bod_salmar", "bod_salabr", "bod_salmay", "bod_saljun",
        "bod_saljul", "bod_salago", "bod_salsep", "bod_saloct", "bod_salnov", "bod_saldic",
        "bod_salen2", "bod_salfe2", "bod_salma2", "bod_salab2", "bod_salmy2", "bod_salju2",
    ];

    protected $periodoAnterior = ["bod_salen2", "bod_salfe2", "bod_salma2", "bod_salab2", "bod_salmy2", "bod_salju2"];

    public function handle($context)
    {
        Log::info('SaldoBodegaRestTransitoHandler: Iniciando el proceso de ajuste de transito.');

        $context->guiaRecepcion->iterarDetalle(function ($detalle) {
            try {
                $producto = $this->getOrCreateProducto($detalle->gui_produc, $detalle->gui_boddes);
                $this->actualizarStockTransito($producto, $detalle->gui_canrep);
            } catch (Exception $e) {
                Log::error('Error en SaldoBodegaRestTransitoHandler', ['error' => $e->getMessage()]);
            }
        });

        Log::info('SaldoBodegaRestTransitoHandler: Proceso finalizado exitosamente.');
    }

    /**
     * Obtiene o crea un producto en la bodega.
     */
    private function getOrCreateProducto($codigoProducto, $bodega)
    {
        $producto = cmsalbod::where('bod_produc', $codigoProducto)
            ->where('bod_bodega', $bodega)
            ->where('bod_ano', Carbon::now()->year)
            ->first();

        if (!$producto) {
            Log::info("Producto no encontrado, creando nuevo registro", ['producto' => $codigoProducto]);
            return $this->crearNuevoProducto($codigoProducto, $bodega);
        }

        return $producto;
    }

    /**
     * Actualiza el stock de tránsito reduciendo la cantidad recepcionada.
     */
    private function actualizarStockTransito($producto, $cantidadRecepcionada)
    {
        $now = Carbon::now();
        $periodo = $this->determinarPeriodo($now);
        $campoPeriodo = $this->getCampoPeriodo($now->month, $periodo);

        Log::info('Actualizando stock en periodo', [
            'campoPeriodo' => $campoPeriodo,
            'cantidadRecepcionada' => $cantidadRecepcionada
        ]);

        $producto->bod_stockb -= $cantidadRecepcionada;
        $producto->bod_stolog -= $cantidadRecepcionada;
        $producto->$campoPeriodo -= $cantidadRecepcionada;

        $this->replicarEnPeriodosInferiores($producto, $campoPeriodo, $cantidadRecepcionada);

        $producto->save();
        Log::info('Producto actualizado exitosamente', ['producto' => $producto->toArray()]);
    }

    /**
     * Determina si el periodo es actual o anterior.
     */
    private function determinarPeriodo($now)
    {
        $yearParSis = Carbon::createFromFormat('Y-m-d', vpparsis::first()->par_fechas)->year;
        return $now->year === $yearParSis ? 'actual' : 'anterior';
    }

    /**
     * Obtiene el campo del periodo según el mes.
     */
    private function getCampoPeriodo($month, $periodo)
    {
        return $periodo === 'actual'
            ? $this->periodoActual[$month - 1]
            : $this->periodoAnterior[$month - 1];
    }

    /**
     * Replica la actualización en periodos inferiores.
     */
    private function replicarEnPeriodosInferiores($producto, $campoPeriodo, $cantidad)
    {
        $periodos = in_array($campoPeriodo, $this->periodoAnterior) ? $this->periodoAnterior : $this->periodoActual;

        $indice = array_search($campoPeriodo, $periodos);

        for ($i = $indice + 1; $i < count($periodos); $i++) {
            $producto->{$periodos[$i]} -= $cantidad;
        }
    }

    /**
     * Crea un nuevo producto en la bodega con valores iniciales.
     */
    private function crearNuevoProducto($codigoProducto, $bodega)
    {
        $producto = new cmsalbod([
            'bod_ano' => Carbon::now()->year,
            'bod_produc' => $codigoProducto,
            'bod_bodega' => $bodega,
            'bod_stockb' => 0,
            'bod_stolog' => 0,
        ]);

        foreach (array_merge($this->periodoActual, $this->periodoAnterior) as $campo) {
            $producto->$campo = 0;
        }

        $producto->save();
        return $producto;
    }
}
