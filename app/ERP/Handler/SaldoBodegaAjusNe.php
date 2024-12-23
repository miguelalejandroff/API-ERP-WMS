<?php

namespace App\ERP\Handler;

use App\ERP\Build\Handler;
use App\Models\cmsalbod;
use App\Models\vpparsis;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SaldoBodegaAjusNe extends Handler
{
    public function handle($context)
    {
        Log::info('SaldoBodegaAjusNe ejecutado');

        $documentoDetalle = $context->ajusteNegativo->documentoDetalle;
        $year = Carbon::now()->year;
        $month = Carbon::now()->month;

        // Determinar periodo actual o anterior
        $parYear = Carbon::createFromFormat('Y-m-d', vpparsis::first()->par_fechas)->year;
        $periodo = ($year === $parYear) ? $this->getPeriodos()['actual'] : $this->getPeriodos()['anterior'];

        // Procesar cada detalle
        foreach ($documentoDetalle as $detalle) {
            $this->procesarDetalle($detalle, $year, $month, $periodo);
        }
    }

    private function procesarDetalle($detalle, $year, $month, $periodo)
    {
        $codigoProducto = $detalle['codigoProducto'];
        $bodegaOrigen = $detalle['BodegaOrigen'];
        $cantidad = $detalle['cantidad'];

        Log::info('Procesando detalle', [
            'codigoProducto' => $codigoProducto,
            'bodegaOrigen' => $bodegaOrigen,
            'cantidad' => $cantidad,
        ]);

        DB::transaction(function () use ($codigoProducto, $bodegaOrigen, $cantidad, $year, $periodo) {
            $saldoBodega = cmsalbod::firstOrCreate(
                ['bod_produc' => $codigoProducto, 'bod_bodega' => $bodegaOrigen, 'bod_ano' => $year],
                $this->initializeBodega($codigoProducto, $bodegaOrigen)
            );

            // Actualizar los valores del saldo
            $saldoBodega->increment('bod_stockb', $cantidad);
            $saldoBodega->increment('bod_stolog', $cantidad);

            foreach ($periodo as $column) {
                $saldoBodega->increment($column, $cantidad);
            }

            Log::info('Saldo actualizado correctamente', [
                'bod_stockb' => $saldoBodega->bod_stockb,
                'bod_stolog' => $saldoBodega->bod_stolog,
            ]);
        });
    }

    private function initializeBodega($codigoProducto, $bodega)
    {
        return array_merge([
            'bod_ano' => Carbon::now()->year,
            'bod_produc' => $codigoProducto,
            'bod_bodega' => $bodega,
            'bod_stockb' => 0,
            'bod_stolog' => 0,
            'bod_storep' => 0,
            'bod_stomax' => 0,
            'bod_salini' => 0,
        ], $this->initializePeriodos());
    }

    private function initializePeriodos()
    {
        $periodos = array_merge($this->getPeriodos()['actual'], $this->getPeriodos()['anterior']);
        return array_fill_keys($periodos, 0);
    }

    private function getPeriodos()
    {
        return [
            'actual' => [
                "bod_salene", "bod_salfeb", "bod_salmar", "bod_salabr", "bod_salmay",
                "bod_saljun", "bod_saljul", "bod_salago", "bod_salsep", "bod_saloct",
                "bod_salnov", "bod_saldic", "bod_salen2", "bod_salfe2", "bod_salma2",
                "bod_salab2", "bod_salmy2", "bod_salju2",
            ],
            'anterior' => ["bod_salen2", "bod_salfe2", "bod_salma2", "bod_salab2", "bod_salmy2", "bod_salju2"],
        ];
    }
}
