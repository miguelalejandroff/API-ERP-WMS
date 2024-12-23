<?php

namespace App\ERP\Handler;

use App\Models\cmproductos;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class MaestroProductoHandler
 * Maneja la actualización de productos en el maestro de productos.
 */
class MaestroProductoHandler
{
    private array $datosActualizacion = [];
    private cmproductos $producto;
    private float $precio;
    private float $cantidad;
    private string $fecha;
    private string $documento;

    public function handle($context)
    {
        Log::info('Iniciando procesamiento en MaestroProductoHandler');

        $context->guiaCompra->iterarDetalle(function ($detalle) use ($context) {
            $this->producto = $this->obtenerProducto($detalle->gui_produc);
            $this->prepararDatos($context, $detalle);
            $this->actualizarProducto();
        });

        Log::info('Finalizando procesamiento en MaestroProductoHandler');
    }

    private function obtenerProducto(string $codigoProducto): cmproductos
    {
        $producto = cmproductos::where('pro_codigo', $codigoProducto)
            ->where('pro_anomes', Carbon::now()->format('Ym'))
            ->first();

        if (!$producto) {
            Log::error("Producto no encontrado: {$codigoProducto}");
            throw new Exception("Producto no encontrado: {$codigoProducto}");
        }

        return $producto;
    }

    private function prepararDatos($context, $detalle): void
    {
        $this->precio = round($detalle->gui_preuni, 2);
        $this->cantidad = round($detalle->gui_canord, 2);
        $this->fecha = $context->recepcionWms->getDocumento('fechaRecepcionWMS')->format('Y-m-d H:i');
        $this->documento = $detalle->gui_numero;

        $this->actualizaPrecioAlto();
        $this->actualizaUltimaCompra();
        $this->actualizaStockYCostoMedio();
    }

    private function actualizarProducto(): void
    {
        DB::transaction(function () {
            if ($this->producto->update($this->datosActualizacion)) {
                Log::info("Producto {$this->producto->pro_codigo} actualizado correctamente");
            } else {
                Log::error("Error al actualizar el producto: {$this->producto->pro_codigo}");
            }
        });
    }

    private function actualizaPrecioAlto(): void
    {
        if ($this->precio > (float) $this->producto->pro_comaal) {
            $this->datosActualizacion['pro_comaal'] = $this->precio;
            $this->datosActualizacion['pro_femaal'] = $this->fecha;
            $this->datosActualizacion['pro_domaal'] = $this->documento;
        }
    }

    private function actualizaUltimaCompra(): void
    {
        $fechaActual = Carbon::parse($this->fecha);
        $fechaUltimaCompra = $this->producto->pro_feulco ? Carbon::parse($this->producto->pro_feulco) : null;

        if (!$fechaUltimaCompra || $fechaActual->gte($fechaUltimaCompra)) {
            $this->datosActualizacion['pro_coulco'] = $this->precio;
            $this->datosActualizacion['pro_feulco'] = $this->fecha;
            $this->datosActualizacion['pro_doulco'] = $this->documento;
        }
    }

    private function actualizaStockYCostoMedio(): void
    {
        $nuevoStock = round($this->producto->pro_stockp + $this->cantidad, 2);
        $costoMedio = $this->calcularCostoMedio($nuevoStock);

        $this->datosActualizacion['pro_stockp'] = $nuevoStock;
        $this->datosActualizacion['pro_cosmed'] = round($costoMedio, 2);
    }

    private function calcularCostoMedio(float $nuevoStock): float
    {
        if ($this->producto->pro_stockp <= 0) {
            return $this->precio;
        }

        $costoTotalActual = $this->producto->pro_stockp * $this->producto->pro_cosmed;
        $costoTotalNuevo = $this->cantidad * $this->precio;

        return ($costoTotalActual + $costoTotalNuevo) / $nuevoStock;
    }
}
