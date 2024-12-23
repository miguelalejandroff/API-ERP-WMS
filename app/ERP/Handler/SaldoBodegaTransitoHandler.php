<?php

namespace App\ERP\Handler;

use App\ERP\Build\Handler;
use App\Models\cmsalbod;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use App\Models\vpparsis;

class SaldoBodegaTransitoHandler extends Handler
{
    /**
     * @var array array para actualizar
     */
    private $array = [];

    /**
     * @var cmsalbod Instancia del producto.
     */
    private $producto;

    /**
     * @var string Periodo correspondiente.
     */
    private $periodo;


    /**
     * @var int Cantidad actual del producto.
     */
    private $cantidad = 0;

    /**
     * @var int Cantidad recepcionada del producto.
     */
    private $cantidadSolicitada = 0;


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
        "bod_salmy2", //Inicio apertura
        "bod_salju2",
    ];

    /**
     * @var array Nombres de los campos de periodo anterior.
     */
    protected $periodoAnterior = [
        "bod_salen2",
        "bod_salfe2",
        "bod_salma2",
        "bod_salab2",
        "bod_salmy2", //Inicio apertura
        "bod_salju2",
    ];


    public function handle($context)
    {
        Log::info('SaldoBodegaHandler', ['message' => 'Manejador SaldoBodegaHandler ejecutado']);
        $context->guiaCompra->iterarDetalle(function ($detalle) use ($context) {
            $this->producto = $this->getBodegaModel($detalle->gui_produc, $detalle->gui_boddes);
            $isActual = false;
            $this->updateModelo($context, $detalle, $isActual);
        });
    }

    /**
     * Recupera un producto por su código.
     * 
     * @param string $codigoProducto Código del producto.
     * @return cmsalbod Producto recuperado.
     */


    private function getBodegaModel($codigoProducto)
    {
        try {
            Log::info('SaldoBodegaHandler', ['message' => 'Buscando producto con código: ' . $codigoProducto]);
            Log::info('SaldoBodegaHandler', ['sql' => cmsalbod::where('bod_produc', $codigoProducto)->where('bod_bodega', '29')->where('bod_ano', Carbon::now()->format('Ym'))->toSql()]);

            $producto = cmsalbod::where('bod_produc', $codigoProducto)->where('bod_bodega', '29')->where('bod_ano', Carbon::now()->format('Y'))->first();

            if (!$producto) {
                $producto = $this->createBodega($codigoProducto);
            }
            Log::info('SaldoBodegaHandler', ['message' => 'Producto encontrado:', 'producto' => $producto]);
            return $producto;
        } catch (Exception $e) {
            $logContext = ['codigoProducto' => $codigoProducto];
            Log::error('SaldoBodegaHandler', ['message' => 'Error al obtener producto: ' . $e->getMessage(), 'context' => $logContext]);
            throw $e;
        }
    }



    /**
     * Actualiza los datos del producto.
     * @param object $context Contexto con la información para la actualización.
     */
    private function updateModelo($context, $detalle, &$isActual)
    {
        $now = Carbon::now();
        $this->cantidadSolicitada = $detalle->gui_canord;

        // Agregar log antes de obtener el periodo
        Log::info('SaldoBodegaHandler', ['message' => 'Obteniendo periodo']);

        $this->periodo = $this->getPeriodo($now, $isActual);

        Log::info('SaldoBodegaHandler', ['message' => 'Periodo obtenido:', 'periodo' => $this->periodo]);
        Log::info('SaldoBodegaHandler', ['message' => 'isPeriodoActual:', 'isPeriodoActual' => $isActual]);

        $campoPeriodo = $isActual ? $this->getPeriodoActual($now->month) : $this->getPeriodoAnterior($now->month);

        Log::info('SaldoBodegaHandler', ['message' => 'Campo de periodo obtenido:', 'campoPeriodo' => $campoPeriodo]);

        $this->$campoPeriodo = $this->cantidadSolicitada;
        
        // Agregar log después de asignar el valor a $campoPeriodo
        Log::info('SaldoBodegaHandler', ['message' => 'Valor asignado a campo de periodo:', 'valor' => $this->$campoPeriodo]);

        if ($this->producto->update($this->array)) {
            Log::info('SaldoBodegaHandler', ['message' => 'Producto actualizado con éxito.']);
            $this->producto->bod_stockb += $this->cantidadSolicitada;
            $this->producto->bod_stolog += $this->cantidadSolicitada;
            $this->producto->$campoPeriodo += $this->cantidadSolicitada;
            // Replicar el valor en las filas de abajo
            $this->replicarValorEnFilasDeAbajo($this->producto, $campoPeriodo, $this->cantidadSolicitada);

            // Log para imprimir los valores finales
            Log::info('SaldoBodegaHandler', [
                'message' => 'Valores finales de bod_stockb y bod_stolog',
                'bod_stockb' => $this->producto->bod_stockb,
                'bod_stolog' => $this->producto->bod_stolog,
            ]); 
            Log::info('SaldoBodegaHandler', [
                'message' => 'Valores finales de ' . $campoPeriodo,
                $campoPeriodo => $this->producto->$campoPeriodo,
            ]);

            // Guardar los cambios en la base de datos
            $this->producto->save();
        } else {
            Log::error('SaldoBodegaHandler', ['message' => 'Error al actualizar el producto.']);
        }
    }

    private function replicarValorEnFilasDeAbajo($producto, $campoPeriodo, $valor)
    {
        $periodo = null;
        $periodos = null;
    
        if (in_array($campoPeriodo, $this->periodoAnterior)) {
            $periodo = 'anterior';
            $periodos = $this->periodoAnterior;
        } elseif (in_array($campoPeriodo, $this->periodoActual)) {
            $periodo = 'actual';
            $periodos = $this->periodoActual;
        }
    
        if ($periodo && $periodos) {
            // Obtener el índice del campo actual
            $indiceCampoActual = array_search($campoPeriodo, $periodos);
            $valorCampoActual = $producto->$campoPeriodo;
    
            // Replicar el valor en las filas de abajo
            for ($i = $indiceCampoActual + 1; $i < count($periodos); $i++) {
                $campoActual = $periodos[$i];
    
                Log::info('Campo actual: ' . $campoActual);
                Log::info('Valor a asignar: ' . $valorCampoActual);
                // Actualizar el valor de la propiedad usando el método mágico __set
                $producto->$campoActual = $valorCampoActual;
    
                // Alternativamente, puedes usar el siguiente enfoque si __set no está disponible
                // $producto->{$campoActual} = $valor;
            }
        }
    }
    
    

    /**
     * Devuelve el campo de periodo correspondiente a la fecha dada.
     *
     * @param Carbon $now Fecha actual.
     * @return string Campo de periodo.
     */
    protected function getPeriodo($now, &$isActual)
    {
        $fechaParSis = Carbon::createFromFormat('Y-m-d', vpparsis::first()->par_fechas);
        Log::info('SaldoBodegaHandler', ['message' => 'Fecha par_sis:', 'fechaParSis' => $fechaParSis]);

        $isActual = ($now->year == $fechaParSis->year);
        Log::info('SaldoBodegaHandler', ['message' => 'Comparando años:', 'nowYear' => $now->year, 'parSisYear' => $fechaParSis->year, 'isActual' => $isActual]);

        return $isActual ? $this->getPeriodoActual($now->month) : $this->getPeriodoAnterior($now->month);
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
     * Crea un nuevo modelo de Bodega para los parámetros dados.
     *
     * @param string $bodega Bodega.
     * @param string $producto Producto.
     * @return cmsalbod Modelo de Bodega.
     */
    private function createBodega($codigoProducto, $cantidad = 0)
    {
        $saldoBodega = new cmsalbod();

        $saldoBodega->bod_ano = Carbon::now()->year;
        $saldoBodega->bod_produc = $codigoProducto;
        $saldoBodega->bod_bodega = '29';

        $saldoBodega->bod_salini = 0;
        $saldoBodega->bod_stockb = $cantidad;
        $saldoBodega->bod_stolog = $cantidad;
        $saldoBodega->bod_storep = 0;
        $saldoBodega->bod_stomax = 0;

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
