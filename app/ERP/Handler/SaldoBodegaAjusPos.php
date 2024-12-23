<?php

namespace App\ERP\Handler;

use App\ERP\Build\Handler;
use App\Models\cmsalbod;
use App\Models\vpparsis;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SaldoBodegaAjusPos extends Handler
{
    public function handle($context)
    {
        Log::info('SaldoBodegaAjusPos iniciado');

        $documentoDetalle = $context->ajustePositivo->documentoDetalle;
        $year = Carbon::now()->year;
        $month = Carbon::now()->month;

        $periodo = $this->determinarPeriodo();

        foreach ($documentoDetalle as $detalle) {
            $this->procesarDetalle($detalle, $year, $periodo);
        }
    }

    private function determinarPeriodo(): array
    {
        $parYear = Carbon::createFromFormat('Y-m-d', vpparsis::first()->par_fechas)->year;
        $tipoPeriodo = (Carbon::now()->year === $parYear) ? 'actual' : 'anterior';

        return $this->getPeriodos($tipoPeriodo);
    }

    private function procesarDetalle($detalle, $year, $periodo)
    {
        $codigoProducto = $detalle['codigoProducto'];
        $bodegaOrigen = $detalle['BodegaOrigen'];
        $cantidad = $detalle['cantidad'];

        Log::info('Procesando detalle', compact('codigoProducto', 'bodegaOrigen', 'cantidad'));

        DB::transaction(function () use ($codigoProducto, $bodegaOrigen, $cantidad, $year, $periodo) {
            $saldoBodega = cmsalbod::firstOrCreate(
                ['bod_produc' => $codigoProducto, 'bod_bodega' => $bodegaOrigen, 'bod_ano' => $year],
                $this->initializeBodega($codigoProducto, $bodegaOrigen)
            );

            // Actualizar saldos
            $saldoBodega->increment('bod_stockb', $cantidad);
            $saldoBodega->increment('bod_stolog', $cantidad);

            foreach ($periodo as $column) {
                $saldoBodega->increment($column, $cantidad);
            }

            Log::info('Saldo actualizado correctamente', [
                'bod_stockb' => $saldoBodega->bod_stockb,
                'bod_stolog' => $saldoBodega->bod_stolog,
                'periodo' => $periodo,
            ]);
        });
    }

    private function initializeBodega($codigoProducto, $bodega): array
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
        ], array_fill_keys($this->getPeriodos('actual'), 0), array_fill_keys($this->getPeriodos('anterior'), 0));
    }

    private function getPeriodos($tipo): array
    {
        $periodos = [
            'actual' => [
                "bod_salene", "bod_salfeb", "bod_salmar", "bod_salabr",
                "bod_salmay", "bod_saljun", "bod_saljul", "bod_salago",
                "bod_salsep", "bod_saloct", "bod_salnov", "bod_saldic",
                "bod_salen2", "bod_salfe2", "bod_salma2", "bod_salab2",
                "bod_salmy2", "bod_salju2",
            ],
            'anterior' => ["bod_salen2", "bod_salfe2", "bod_salma2", "bod_salab2", "bod_salmy2", "bod_salju2"],
        ];

        return $periodos[$tipo] ?? [];
    }
}
