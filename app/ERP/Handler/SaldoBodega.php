<?php

namespace App\ERP\Handler;

use App\Models\cmsalbod;
use App\Models\vpparsis;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Class SaldoBodega
 *
 * Maneja la lógica para obtener, crear y actualizar saldos de bodega.
 */
class SaldoBodega
{
    /**
     * Periodos actuales y anteriores.
     *
     * @var array
     */
    protected $periodos = [
        'actual' => [
            "bod_salene", "bod_salfeb", "bod_salmar", "bod_salabr", "bod_salmay",
            "bod_saljun", "bod_saljul", "bod_salago", "bod_salsep", "bod_saloct",
            "bod_salnov", "bod_saldic"
        ],
        'anterior' => [
            "bod_salen2", "bod_salfe2", "bod_salma2", "bod_salab2", "bod_salmy2", "bod_salju2"
        ]
    ];

    /**
     * Determina el periodo correspondiente basado en la fecha actual y parámetros.
     *
     * @param Carbon $now
     * @return string
     */
    protected function getPeriodo(Carbon $now)
    {
        $parYear = Carbon::createFromFormat('Y-m-d', vpparsis::first()->par_fechas)->year;
        $tipo = ($now->year === $parYear) ? 'actual' : 'anterior';

        return $this->periodos[$tipo][$now->month - 1] ?? null;
    }

    /**
     * Obtiene el modelo de Bodega o lo crea si no existe.
     *
     * @param string $bodega
     * @param string $producto
     * @param int $cantidad
     * @return cmsalbod
     */
    protected function getBodegaModel($bodega, $producto, $cantidad = 0)
    {
        return cmsalbod::firstOrCreate(
            ['bod_produc' => $producto, 'bod_bodega' => $bodega, 'bod_ano' => Carbon::now()->year],
            $this->initializeBodegaAttributes($bodega, $producto, $cantidad)
        );
    }

    /**
     * Inicializa los atributos de una nueva bodega.
     *
     * @param string $bodega
     * @param string $producto
     * @param int $cantidad
     * @return array
     */
    private function initializeBodegaAttributes($bodega, $producto, $cantidad)
    {
        $attributes = [
            'bod_ano' => Carbon::now()->year,
            'bod_produc' => $producto,
            'bod_bodega' => $bodega,
            'bod_salini' => 0,
            'bod_stockb' => 0,
            'bod_stolog' => 0,
            'bod_storep' => $cantidad,
            'bod_stomax' => $cantidad,
        ];

        foreach (array_merge($this->periodos['actual'], $this->periodos['anterior']) as $periodo) {
            $attributes[$periodo] = 0;
        }

        return $attributes;
    }

    /**
     * Actualiza el modelo con los valores de stock y periodo.
     *
     * @param cmsalbod $modelo
     * @param int $cantidad
     * @param string $periodo
     */
    public function updateModelo(cmsalbod $modelo, int $cantidad, string $periodo)
    {
        DB::transaction(function () use ($modelo, $cantidad, $periodo) {
            $modelo->increment('bod_stockb', $cantidad);
            $modelo->increment('bod_stolog', $cantidad);
            $modelo->increment($periodo, $cantidad);
        });
    }

    /**
     * Crea un nuevo modelo de Bodega si no existe y devuelve el modelo.
     *
     * @param string $bodega
     * @param string $producto
     * @param int $cantidad
     * @return cmsalbod
     */
    protected function createBodega($bodega, $producto, $cantidad = 0)
    {
        return cmsalbod::create($this->initializeBodegaAttributes($bodega, $producto, $cantidad));
    }
}
