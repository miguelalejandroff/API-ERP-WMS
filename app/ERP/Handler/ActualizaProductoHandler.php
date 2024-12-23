<?php

namespace App\ERP\Handler;

use App\Models\cmproductos;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Exception;

class ActualizaProductoHandler
{
    /**
     * Actualiza los datos del producto.
     *
     * @param string $codigoProducto Código del producto.
     * @param float $precio Precio unitario del producto.
     * @param float $cantidad Cantidad ordenada.
     * @param string $fecha Fecha de la operación (Y-m-d).
     * @param string $documento Documento asociado.
     * @param float $cantidadRecepcionada Cantidad recepcionada.
     * @throws Exception
     */
    public function handle($codigoProducto, $precio, $cantidad, $fecha, $documento, $cantidadRecepcionada): void
    {
        try {
            $producto = $this->obtenerProducto($codigoProducto);

            if (!$producto) {
                throw new Exception("Producto no encontrado: {$codigoProducto}");
            }

            $datosActualizados = [];
            $precio = round($precio, 2);
            $cantidad = round($cantidad, 2);

            // Actualizar precio más alto
            $this->actualizarPrecioAlto($producto, $precio, $fecha, $documento, $datosActualizados);

            // Actualizar última compra
            $this->actualizarUltimaCompra($producto, $precio, $fecha, $documento, $datosActualizados);

            // Calcular costo medio
            $costoMedio = $this->calcularCostoMedio($producto, $precio, $cantidad);
            $datosActualizados['pro_stockp'] = round($producto->pro_stockp + $cantidadRecepcionada, 2);
            $datosActualizados['pro_cosmed'] = round($costoMedio, 2);

            // Actualizar producto
            $producto->update($datosActualizados);

            Log::info('Producto actualizado correctamente', [
                'codigoProducto' => $codigoProducto,
                'datosActualizados' => $datosActualizados,
            ]);
        } catch (Exception $e) {
            Log::error('Error al actualizar producto', [
                'codigoProducto' => $codigoProducto,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Obtiene el producto de la base de datos.
     *
     * @param string $codigoProducto
     * @return cmproductos|null
     */
    protected function obtenerProducto($codigoProducto)
    {
        return cmproductos::where('pro_codigo', $codigoProducto)
            ->where('pro_anomes', Carbon::now()->format('Ym'))
            ->first();
    }

    /**
     * Actualiza el precio más alto del producto.
     *
     * @param cmproductos $producto
     * @param float $precio
     * @param string $fecha
     * @param string $documento
     * @param array &$datosActualizados
     */
    private function actualizarPrecioAlto($producto, $precio, $fecha, $documento, array &$datosActualizados): void
    {
        if ($precio > (float)$producto->pro_comaal) {
            $datosActualizados['pro_comaal'] = $precio;
            $datosActualizados['pro_femaal'] = $fecha;
            $datosActualizados['pro_domaal'] = $documento;
        }
    }

    /**
     * Actualiza la información de la última compra del producto.
     *
     * @param cmproductos $producto
     * @param float $precio
     * @param string $fecha
     * @param string $documento
     * @param array &$datosActualizados
     */
    private function actualizarUltimaCompra($producto, $precio, $fecha, $documento, array &$datosActualizados): void
    {
        $fechaActual = Carbon::parse($fecha);
        $fechaUltimaCompra = $producto->pro_feulco ? Carbon::parse($producto->pro_feulco) : null;

        if (is_null($fechaUltimaCompra) || $fechaActual->gte($fechaUltimaCompra)) {
            $datosActualizados['pro_coulco'] = $precio;
            $datosActualizados['pro_feulco'] = $fecha;
            $datosActualizados['pro_doulco'] = $documento;
        }
    }

    /**
     * Calcula el costo medio del producto.
     *
     * @param cmproductos $producto
     * @param float $precio
     * @param float $cantidad
     * @return float
     */
    private function calcularCostoMedio($producto, $precio, $cantidad): float
    {
        if ($producto->pro_stockp <= 0) {
            return $precio;
        }

        return (($cantidad * $precio) + ($producto->pro_stockp * $producto->pro_cosmed)) / ($cantidad + $producto->pro_stockp);
    }
}
