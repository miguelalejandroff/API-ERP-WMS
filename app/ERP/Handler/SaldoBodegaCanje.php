<?php

namespace App\ERP\Handler;

use App\ERP\Build\Handler;
use App\Models\cmsalbod;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\vpparsis;

class SaldoBodegaCanje extends Handler
{

    public function handle($context)
    {
        $documentoDetalle = $context->traspasoBodega->documentoDetalle;

        $year = Carbon::now()->year;
        $month = Carbon::now()->format('m');

        $periodoActual = $this->getPeriodoActual($month);
        $periodoAnterior = $this->getPeriodoAnterior($month);

        $parYear = Carbon::createFromFormat('Y-m-d', vpparsis::first()->par_fechas)->year;

        $periodo = ($year === $parYear) ? $periodoActual : $periodoAnterior;

        foreach ($documentoDetalle as $detalle) {
            $codigoProducto = $detalle['codigoProducto'];
            $bodegaOrigen = $detalle['BodegaOrigen'];
            $bodegaDestino = $detalle['BodegaDestino'];
            $cantidad = $detalle['cantidad'];

            // Log detalles antes de actualizar
            Log::info('Procesando documento detalle', [
                'codigoProducto' => $codigoProducto,
                'bodegaOrigen' => $bodegaOrigen,
                'bodegaDestino' => $bodegaDestino,
                'cantidad' => $cantidad,
                'year' => $year,
                'periodo' => $periodo,
                'month' => $month,
            ]);

            // Crear o recuperar bodegaDestino y actualizar el saldo
            $saldoBodegaDestino = cmsalbod::where('bod_produc', $codigoProducto)
                ->where('bod_bodega', $bodegaDestino)
                ->where('bod_ano', $year)
                ->first();

            if (!$saldoBodegaDestino) {
                Log::info("No se encontró saldoBodega para bodegaDestino. Creando...");
                $saldoBodegaDestino = $this->createBodega($codigoProducto, $bodegaDestino, $cantidad);
            }
            $this->actualizarSaldoBodega($codigoProducto, $bodegaDestino, $cantidad, $year, $periodo, 'positivo');

            // Actualizar el saldo de la bodegaOrigen
            $this->actualizarSaldoBodega($codigoProducto, $bodegaOrigen, $cantidad, $year, $periodo, 'restar');
        }
    }

    public function actualizarSaldoBodega($codigoProducto, $bodega, $cantidad, $year, $periodo, $operacion)
    {
        // Obtener el saldoBodega correspondiente
        $saldoBodega = cmsalbod::where('bod_produc', $codigoProducto)
            ->where('bod_bodega', $bodega)
            ->where('bod_ano', $year)
            ->first();
    
        if (!$saldoBodega) {
            Log::info("No se encontró saldoBodega para bodega $bodega. Creando...");
            $saldoBodega = $this->createBodega($codigoProducto, $bodega);
        }
    
        // Verificar que se encontró o creó el saldoBodega
        if ($saldoBodega instanceof cmsalbod) {
            // Log antes de actualizar
            Log::info('SaldoBodega antes de la actualización:', $saldoBodega->toArray());
    
            $ajuste = ($operacion === 'restar') ? -$cantidad : $cantidad;
    
            // Actualizar los valores según la operación
            $saldoBodega->bod_stockb += $ajuste;
            $saldoBodega->bod_stolog += $ajuste;
    
            foreach ($periodo as $column) {
                $saldoBodega->{$column} += $ajuste;
            }
    
            // Guardar cambios
            $saldoBodega->save();
    
            // Log después de la actualización
            Log::info("SaldoBodega actualizado:", $saldoBodega->toArray());
        } else {
            Log::error("El saldo de bodega no se encontró o no se creó correctamente para el producto: $codigoProducto en la bodega: $bodega para el año: $year");
        }
    }
    

    private function getPeriodoActual($month)
    {
        $periodoActual = [
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
            "bod_salmy2", //Inicio apertura
            "bod_salju2",
        ];

        return array_slice($periodoActual, $month - 1);
    }

    private function getPeriodoAnterior($month)
    {
        $periodoAnterior = [
            "bod_salen2",
            "bod_salfe2",
            "bod_salma2",
            "bod_salab2",
            "bod_salmy2", //Inicio apertura
            "bod_salju2",
        ];

        return array_slice($periodoAnterior, $month - 1);
    }

    private function createBodega($codigoProducto, $bodega, $cantidad = 0)
    {
        $saldoBodega = new cmsalbod();
    
        $saldoBodega->bod_ano = Carbon::now()->year;
        $saldoBodega->bod_produc = $codigoProducto;
        $saldoBodega->bod_bodega = $bodega;
    
        $saldoBodega->bod_salini = 0;
        $saldoBodega->bod_stockb = 0;
        $saldoBodega->bod_stolog = 0;
        $saldoBodega->bod_storep = $cantidad;
        $saldoBodega->bod_stomax = $cantidad;
        $saldoBodega->bod_salene = 0;
        $saldoBodega->bod_salfeb = 0;
        $saldoBodega->bod_salmar = 0;
        $saldoBodega->bod_salabr = 0;
        $saldoBodega->bod_salmay = 0;
        $saldoBodega->bod_saljun = 0;
        $saldoBodega->bod_saljul = 0;
        $saldoBodega->bod_salago = 0;
        $saldoBodega->bod_salsep = 0;
        $saldoBodega->bod_saloct = 0;
        $saldoBodega->bod_salnov = 0;
        $saldoBodega->bod_saldic = 0;
        $saldoBodega->bod_salen2 = 0;
        $saldoBodega->bod_salfe2 = 0;
        $saldoBodega->bod_salma2 = 0;
        $saldoBodega->bod_salab2 = 0;
        $saldoBodega->bod_salju2 = 0;
    
        $periodoActual = $this->getPeriodoActual(Carbon::now()->month);
        $periodoAnterior = $this->getPeriodoAnterior(Carbon::now()->month);
    
        foreach ($periodoActual as $month) {
            $saldoBodega->$month = 0;
        }
    
        foreach ($periodoAnterior as $month) {
            $saldoBodega->$month = 0;
        }
    
        $saldoBodega->save();
    
        return $saldoBodega;
    }    
     
}
