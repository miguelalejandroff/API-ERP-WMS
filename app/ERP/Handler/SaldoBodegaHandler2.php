<?php

namespace App\ERP\Handler;

use App\ERP\Build\Handler;
use App\Models\cmsalbod;
use App\Models\cmdetgui;
use App\Models\vpparsis;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class SaldoBodegaHandler2 extends Handler
{
    private $producto;
    private $cantidadSolicitada;

    /**
     * Devuelve los periodos de bodegas.
     */
    public static function getPeriodos(): array
    {
        return [
            'actual' => [
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
                "bod_salju2"
            ],
            'anterior' => ["bod_salen2", "bod_salfe2", "bod_salma2", "bod_salab2", "bod_salmy2", "bod_salju2"]
        ];
    }

    /**
     * Maneja el contexto y procesa los detalles.
     */
    public function handle($context)
    {
        Log::info('SaldoBodegaHandler2 iniciado');

        $numeroDocumento = $context->recepcionWms->getDocumento('numeroDocumento');
        $tipoDocumentoERP = $context->recepcionWms->getDocumento('tipoDocumentoERP');

        if (!$numeroDocumento || !$tipoDocumentoERP) {
            return $this->logError('Número de documento o tipo de documento no encontrado');
        }

        $detalles = Cache::remember("detalles_{$numeroDocumento}_{$tipoDocumentoERP}", now()->addMinutes(10), function () use ($numeroDocumento, $tipoDocumentoERP) {
            return cmdetgui::where('gui_numero', $numeroDocumento)
                ->where('gui_tipgui', $tipoDocumentoERP)
                ->get();
        });

        if ($detalles->isEmpty()) {
            return $this->logError("No hay detalles para el documento: {$numeroDocumento}");
        }

        foreach ($detalles as $detalle) {
            $this->procesarDetalle($detalle);
        }
    }

    /**
     * Procesa cada detalle individual.
     */
    private function procesarDetalle($detalle)
    {
        $this->producto = $this->getBodegaModel($detalle->gui_produc, $detalle->gui_boddes);
        $this->cantidadSolicitada = $detalle->gui_canrep;

        $periodo = $this->determinarPeriodo();
        $campoPeriodo = $this->obtenerCampoPeriodo($periodo);

        if (!$campoPeriodo) {
            return $this->logError("Periodo no válido para el mes actual.");
        }

        $this->actualizarProducto($campoPeriodo);
    }

    /**
     * Determina si el periodo es actual o anterior.
     */
    private function determinarPeriodo(): string
    {
        $fechaParSis = Carbon::createFromFormat('Y-m-d', vpparsis::first()->par_fechas);
        return Carbon::now()->year == $fechaParSis->year ? 'actual' : 'anterior';
    }

    /**
     * Obtiene el campo de periodo correspondiente.
     */
    private function obtenerCampoPeriodo(string $tipo): ?string
    {
        $periodos = self::getPeriodos();
        $mes = Carbon::now()->month;

        return $periodos[$tipo][$mes - 1] ?? null;
    }

    /**
     * Actualiza el producto incrementando valores.
     */
    private function actualizarProducto($campoPeriodo)
    {
        try {
            $this->producto->increment($campoPeriodo, $this->cantidadSolicitada);
            $this->producto->increment('bod_stockb', $this->cantidadSolicitada);
            $this->producto->increment('bod_stolog', $this->cantidadSolicitada);

            Log::info('Producto actualizado correctamente', [
                'bod_stockb' => $this->producto->bod_stockb,
                'bod_stolog' => $this->producto->bod_stolog,
                $campoPeriodo => $this->producto->$campoPeriodo,
            ]);
        } catch (Exception $e) {
            $this->logError('Error al actualizar el producto: ' . $e->getMessage());
        }
    }

    /**
     * Recupera el modelo de bodega o lo crea si no existe.
     */
    private function getBodegaModel($codigoProducto, $bodega)
    {
        $cacheKey = "bodega_{$codigoProducto}_{$bodega}_" . Carbon::now()->year;

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($codigoProducto, $bodega) {
            return cmsalbod::where('bod_produc', $codigoProducto)
                ->where('bod_bodega', $bodega)
                ->where('bod_ano', Carbon::now()->year)
                ->firstOr(function () use ($codigoProducto, $bodega) {
                    return $this->createBodega($codigoProducto, $bodega);
                });
        });
    }

    /**
     * Crea una nueva instancia de la bodega.
     */
    private function createBodega($codigoProducto, $bodega)
    {
        $saldoBodega = new cmsalbod([
            'bod_ano' => Carbon::now()->year,
            'bod_produc' => $codigoProducto,
            'bod_bodega' => $bodega,
            'bod_stockb' => 0,
            'bod_stolog' => 0,
        ]);

        foreach (array_merge(self::getPeriodos()['actual'], self::getPeriodos()['anterior']) as $campo) {
            $saldoBodega->$campo = 0;
        }

        $saldoBodega->save();
        return $saldoBodega;
    }

    /**
     * Registra un error en el log.
     */
    private function logError($message)
    {
        Log::error($message);
    }
}
