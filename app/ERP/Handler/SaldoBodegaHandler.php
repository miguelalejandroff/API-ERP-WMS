<?php

namespace App\ERP\Handler;

use App\ERP\Build\Handler;
use App\Models\cmsalbod;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\vpparsis;

class SaldoBodegaHandler extends Handler
{
    private $array = [];
    private $producto;
    private $periodo;
    private $cantidadRecepcionada = 0;

    protected $periodoActual = [
        "bod_salene",
        "bod_salfeb",
        "bod_salmar",
        "bod_salabr",
        "bod_salmay",
        "bod_saljun",
        "bod_saljul",
        "bod_salago",
        "bod_salsep",
        "bod_saloct",
        "bod_salnov",
        "bod_saldic",
        "bod_salen2",
        "bod_salfe2",
        "bod_salma2",
        "bod_salab2",
        "bod_salmy2",
        "bod_salju2",
    ];

    protected $periodoAnterior = [
        "bod_salen2",
        "bod_salfe2",
        "bod_salma2",
        "bod_salab2",
        "bod_salmy2",
        "bod_salju2",
    ];

    public function handle($context)
    {
        Log::info('SaldoBodegaHandler ejecutado');
        $context->guiaRecepcion->iterarDetalle(function ($detalle) use ($context) {
            $this->producto = $this->getBodegaModel($detalle->gui_produc, $detalle->gui_boddes);
            $this->updateModelo($context, $detalle);
        });
    }

    private function getBodegaModel($codigoProducto, $bodega)
    {
        $cacheKey = "bodega_{$codigoProducto}_{$bodega}_" . Carbon::now()->year;

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($codigoProducto, $bodega) {
            try {
                return cmsalbod::where([
                    ['bod_produc', $codigoProducto],
                    ['bod_bodega', $bodega],
                    ['bod_ano', Carbon::now()->format('Y')]
                ])->firstOr(function () use ($codigoProducto, $bodega) {
                    return $this->createBodega($codigoProducto, $bodega);
                });
            } catch (Exception $e) {
                Log::error('Error al obtener producto', ['codigo' => $codigoProducto, 'error' => $e->getMessage()]);
                throw $e;
            }
        });
    }

    private function updateModelo($context, $detalle)
    {
        $now = Carbon::now();
        $this->cantidadRecepcionada = $detalle->gui_canrep;

        $isActual = false;
        $this->periodo = $this->getPeriodo($now, $isActual);
        $campoPeriodo = $isActual ? $this->getPeriodoActual($now->month) : $this->getPeriodoAnterior($now->month);

        DB::transaction(function () use ($campoPeriodo) {
            $this->producto->bod_stockb += $this->cantidadRecepcionada;
            $this->producto->bod_stolog += $this->cantidadRecepcionada;
            $this->producto->$campoPeriodo += $this->cantidadRecepcionada;

            $this->replicarValorEnFilasDeAbajo($this->producto, $campoPeriodo);
            $this->producto->save();
        });

        Log::info('Producto actualizado correctamente', [
            'bod_stockb' => $this->producto->bod_stockb,
            'bod_stolog' => $this->producto->bod_stolog,
            $campoPeriodo => $this->producto->$campoPeriodo,
        ]);
    }

    private function replicarValorEnFilasDeAbajo($producto, $campoPeriodo)
    {
        $periodos = in_array($campoPeriodo, $this->periodoAnterior) ? $this->periodoAnterior : $this->periodoActual;

        collect($periodos)->slice(array_search($campoPeriodo, $periodos) + 1)
            ->each(fn($campo) => $producto->$campo = $producto->$campoPeriodo);
    }

    protected function getPeriodo($now, &$isActual)
    {
        $fechaParSis = Carbon::createFromFormat('Y-m-d', vpparsis::first()->par_fechas);
        $isActual = ($now->year == $fechaParSis->year);
        return $isActual ? $this->getPeriodoActual($now->month) : $this->getPeriodoAnterior($now->month);
    }

    protected function getPeriodoActual($month)
    {
        return $this->periodoActual[$month - 1];
    }

    protected function getPeriodoAnterior($month)
    {
        return $this->periodoAnterior[$month - 1];
    }

    private function createBodega($codigoProducto, $bodega, $cantidad = 0)
    {
        $saldoBodega = new cmsalbod([
            'bod_ano' => Carbon::now()->year,
            'bod_produc' => $codigoProducto,
            'bod_bodega' => $bodega,
            'bod_salini' => 0,
            'bod_stockb' => 0,
            'bod_stolog' => 0,
            'bod_storep' => $cantidad,
            'bod_stomax' => $cantidad,
        ]);

        foreach (array_merge($this->periodoActual, $this->periodoAnterior) as $month) {
            $saldoBodega->$month = 0;
        }

        $saldoBodega->save();
        return $saldoBodega;
    }
}
