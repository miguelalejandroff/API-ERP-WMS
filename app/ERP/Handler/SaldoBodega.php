<?php

namespace App\ERP\Handler;

use App\Models\cmsalbod;
use App\Models\vpparsis;
use Carbon\Carbon;

/**
 * Class SaldoBodega
 *
 * Esta clase maneja la lógica para obtener, crear y actualizar saldos de bodega.
 */
class SaldoBodega
{
    /**
     * @var array Nombres de los campos de periodo actual.
     */
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
        "bod_saldic"
    ];

    /**
     * @var array Nombres de los campos de periodo anterior.
     */
    protected $periodoAnterior = [
        "bod_salen2",
        "bod_salfe2",
        "bod_salma2",
        "bod_salab2",
        "bod_salmy2",
        "bod_salju2",
    ];

    /**
     * Devuelve el campo de periodo correspondiente a la fecha dada.
     *
     * @param Carbon $now Fecha actual.
     * @return string Campo de periodo.
     */
    protected function getPeriodo($now)
    {
        if ($now->month < 7 && $now->year != Carbon::createFromFormat('Y-m-d', vpparsis::first()->par_fechas)->year) {
            return $this->getPeriodoAnterior($now->month);
        }

        return $this->getPeriodoActual($now->month);
    }

    /**
     * Devuelve el campo de periodo actual correspondiente al mes dado.
     *
     * @param int $month Mes.
     * @return string Campo de periodo actual.
     */
    protected function getPeriodoActual($month)
    {
        return $this->periodoActual[$month - 1];
    }

    /**
     * Devuelve el campo de periodo anterior correspondiente al mes dado.
     *
     * @param int $month Mes.
     * @return string Campo de periodo anterior.
     */
    protected function getPeriodoAnterior($month)
    {
        return $this->periodoAnterior[$month - 1];
    }

    /**
     * Obtiene el modelo de Bodega para los parámetros dados, o lo crea si no existe.
     *
     * @param string $bodega Bodega.
     * @param string $producto Producto.
     * @return cmsalbod Modelo de Bodega.
     */
    protected function getBodegaModel($bodega, $producto, $cantidad = 0)
    {
        $modelo = cmsalbod::byBodegaProducto($bodega, $producto);

        if (!$modelo) {
            $modelo = $this->createBodega($bodega, $producto, $cantidad = 0);
        }

        return $modelo;
    }

    /**
     * Actualiza el modelo dado con la cantidad y periodo proporcionados.
     *
     * @param cmsalbod $modelo Modelo de Bodega.
     * @param string $fn Método de actualización.
     * @param int $cantidad Cantidad.
     * @param string $periodo Campo de periodo.
     */
    protected function updateModelo($modelo, $fn, $cantidad, $periodo)
    {
        $modelo->$fn('bod_stockb', $cantidad);
        $modelo->$fn('bod_stolog', $cantidad);
        $modelo->$fn($periodo, $cantidad);
    }

    /**
     * Crea un nuevo modelo de Bodega para los parámetros dados.
     *
     * @param string $bodega Bodega.
     * @param string $producto Producto.
     * @return cmsalbod Modelo de Bodega.
     */
    protected function createBodega($bodega, $producto, $cantidad = 0)
    {
        $saldoBodega = new cmsalbod();

        $saldoBodega->bod_ano = Carbon::now()->year;
        $saldoBodega->bod_produc = $producto;
        $saldoBodega->bod_bodega = $bodega;

        $saldoBodega->bod_salini = 0;
        $saldoBodega->bod_stockb = 0;
        $saldoBodega->bod_stolog = 0;
        $saldoBodega->bod_storep = $cantidad;
        $saldoBodega->bod_stomax = $cantidad;

        foreach ($this->periodoActual as $month) {
            $saldoBodega->$month = 0;
        }

        foreach ($this->periodoAnterior as $month) {
            $saldoBodega->$month = 0;
        }

        $saldoBodega->save();

        return $saldoBodega;
    }
}
