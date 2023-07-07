<?php

namespace App\Libs;

use Closure;
use Exception;

/**
 * La clase SaldoOrden se encarga de actualizar los saldos de las órdenes de compra al recibir productos.
 */
class SaldoOrden
{
    /**
     * Crea una nueva instancia de la clase SaldoOrden.
     * 
     * @var mixed $ordenCompra La orden de compra a actualizar.
     * @var mixed $ordenCompraBonificada La orden de compra bonificada a actualizar.
     * @var mixed $producto El producto a recibir.
     * @var float $cantidadRecepcion La cantidad de producto a recibir.
     * @var Closure $catch La función a ejecutar si hay errores.
     * 
     * @throws Exception Si hay problemas al recepcionar los productos.
     */
    public function __construct(
        protected $ordenCompra,
        protected $ordenCompraBonificada,
        protected $producto,
        protected float $cantidadRecepcion = 0,
        protected Closure $catch,
    ) {

        try {
            $cantidadRecepcion =  round($cantidadRecepcion, 2);
            if ($ordenCompraBonificada) {
                $detalleCompraBonificada = $ordenCompraBonificada->buscaProducto($producto)->first();

                if ($detalleCompraBonificada) {

                    $saldoBonificado =  round($detalleCompraBonificada->ord_saldos, 2);

                    if ($saldoBonificado > 0) {

                        if ($saldoBonificado < $cantidadRecepcion) {

                            $detalleCompraBonificada->decrement('ord_saldos', $saldoBonificado);
                            $cantidadRecepcion -= $saldoBonificado;
                        } else {

                            $detalleCompraBonificada->decrement('ord_saldos', $cantidadRecepcion);
                            $cantidadRecepcion -= $cantidadRecepcion;
                        }
                    }
                }
            }
            if ($cantidadRecepcion > 0) {

                $detalleCompra = $ordenCompra->buscaProducto($producto)->first();

                $saldo = (float)$detalleCompra->ord_saldos;

                if ($saldo > 0) {
                    if ($saldo >= $cantidadRecepcion) {
                        $detalleCompra->decrement('ord_saldos', $cantidadRecepcion);
                        $cantidadRecepcion -= $cantidadRecepcion;
                    } else {
                        throw new Exception(
                            "El Producto {$producto} de la orden {$ordenCompra->ord_numcom} tiene un saldo {$saldo} que es menor a la cantidad a recepcionar {$cantidadRecepcion} "
                        );
                    }
                }
            }

            if ($cantidadRecepcion > 0) {
                throw new Exception(
                    "Problemas al recepcionar la totalidad el producto {$producto} de la orden  {$ordenCompra->ord_numcom}"
                );
            }
        } catch (Exception $e) {
            $catch($e->getMessage());
        }
    }
}
