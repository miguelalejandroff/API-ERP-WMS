<?php

namespace App\ERP\Handler;

use App\ERP\Build\Handler;
use App\Models\cmproductos;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;

class MaestroProductoHandler extends Handler
{
    /**
     * @var array array para actualizar
     */
    private $array = [];

    /**
     * @var cmproductos Instancia del producto.
     */
    private $producto;

    /**
     * @var int Precio actual del producto.
     */
    private $precio = 0;

    /**
     * @var int Cantidad actual del producto.
     */
    private $cantidad = 0;

    /**
     * @var int Cantidad recepcionada del producto.
     */
    private $cantidadOrden = 0;

    /**
     * @var string Fecha de la operación.
     */
    private $fecha;

    /**
     * @var string Documento asociado a la operación.
     */
    private $documento;

    public function handle($context)
    {
        Log::info('MaestroProductoHandler', ['message' => 'Inicia Proceso']);
        $context->guiaCompra->iterarDetalle(function ($detalle) use ($context) {
            $this->producto = $this->obtenerProducto($detalle->gui_produc);
            $this->actualizarProducto($context, $detalle);
        });
        Log::info('MaestroProductoHandler', ['message' => 'Finaliza Proceso']);
    }

    /**
     * Recupera un producto por su código.
     * 
     * @param string $codigoProducto Código del producto.
     * @return cmproductos Producto recuperado.
     */


    private function obtenerProducto($codigoProducto)
    {
        try {

            $producto = cmproductos::where('pro_codigo', $codigoProducto)
                ->where('pro_anomes', Carbon::now()->format('Ym'))
                ->first();

            if (!$producto) {
                $errorMessage = "Producto no encontrado: " . $codigoProducto;
                Log::error('MaestroProductoHandler', ['message' => $errorMessage]);
                throw new Exception($errorMessage);
            }

            return $producto;
        } catch (Exception $e) {
            $logContext = ['codigoProducto' => $codigoProducto];
            Log::error('MaestroProductoHandler', ['message' => 'Error al obtener producto: ' . $e->getMessage(), 'context' => $logContext]);
            throw $e;
        }
    }



    /**
     * Actualiza los datos del producto.
     * @param object $context Contexto con la información para la actualización.
     */
    private function actualizarProducto($context, $detalle)
    {
        
        $this->precio = round($detalle->gui_preuni, 2);
        $this->cantidad = round($detalle->gui_canord, 2);
        $this->cantidadOrden = round($detalle->gui_canord, 2);
        $this->fecha = $context->recepcionWms->getDocumento('fechaRecepcionWMS')->format('Y-m-d H:i');
        $this->documento = $detalle->gui_numero;

        $this->preparaDatos();

        if ($this->producto->update($this->array)) {
            Log::info('MaestroProductoHandler', ['message' => 'Producto actualizado con éxito.']);
        } else {
            Log::error('MaestroProductoHandler', ['message' => 'Error al actualizar el producto.']);
        }
    }


    /**
     * Prepara los datos para la actualización del producto.
     */
    protected function preparaDatos()
    {

        $this->actualizaPrecioAlto();
        $this->actualizaUltimaCompra();
        $this->actualizaStockYCostoMedio();
    }

    /**
     * Actualiza el precio más alto registrado para el producto.
     */
    private function actualizaPrecioAlto()
    {
        if ($this->precio > (int)$this->producto->pro_comaal) {
            $this->array['pro_comaal'] = $this->precio;
            $this->array['pro_femaal'] = $this->fecha;
            $this->array['pro_domaal'] = $this->documento;
        }
    }

    /**
     * Actualiza los datos de la última compra del producto.
     */
    private function actualizaUltimaCompra()
    {
        $fechaActual = Carbon::parse($this->fecha);
        $fechaUltimaCompra = Carbon::parse($this->producto->pro_feulco);

        if ($fechaActual->gte($fechaUltimaCompra) || is_null($this->producto->pro_feulco)) {
            $this->array['pro_coulco'] = $this->precio;
            $this->array['pro_feulco'] = $this->fecha;
            $this->array['pro_doulco'] = $this->documento;
        }
    }

    /**
     * Actualiza el stock y el costo medio del producto.
     */
    private function actualizaStockYCostoMedio()
    {
        $costoMedio = $this->calcularCostoMedio();
        $this->array['pro_stockp'] = round($this->producto->pro_stockp + $this->cantidadOrden, 2);
        $this->array['pro_cosmed'] = round($costoMedio, 2);
    }

    /**
     * Calcula el costo medio del prodcuto
     */
    protected function calcularCostoMedio()
    {
        if ($this->producto->pro_stockp < 0) {
            return $this->precio;
        }

        return (($this->cantidad * $this->precio) + ($this->producto->pro_stockp * $this->producto->pro_cosmed)) / ($this->cantidad + $this->producto->pro_stockp);
    }
}
