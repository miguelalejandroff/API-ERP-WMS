<?php

namespace App\Libs;

use App\Models\cmdetord;
use App\Models\cmordcom;
use Illuminate\Contracts\Database\Eloquent\Builder;

class CalcularCosto
{
    public function __construct(
        protected  $model,
        protected  $detalle,
        public float $valorMoneda = 1,
        protected $bonificacion = null,
        public float $precioBonificado = 0,
        public float $cantidadBonificada = 0,
        public float $saldoBonificado = 0,
        public float $precio = 0,
        public float $cantidad = 0,
        public float $saldo = 0,
        public float $cantidadCalculada = 0,
        public float $precioCalculado = 0,
        public float $saldoCalculado = 0,
    ) {
        foreach (get_object_vars($this) as $key => $row) {
            if (!in_array($key, ["model", "detalle"])) {
                $this->$key = $this->$key();
            }
        }
        /*
        $this->valorMoneda = $this->valorMoneda();
        $this->bonificacion = $this->bonificacion();

        if (!is_null($this->bonificacion)) {
            $this->precioBonificado = $this->precioBonificado();
            $this->cantidadBonificada = $this->cantidadBonificada();
            $this->saldoBonificado = $this->saldoBonificado();
        }

        $this->precio = $this->precio();
        $this->cantidad = $this->cantidad();
        $this->saldo = $this->saldo();

        $this->cantidadCalculada = $this->cantidadCalculada();
        $this->precioCalculado = $this->precioCalculado();
        $this->saldoCalculado = $this->saldoCalculado();
        */
        unset($this->model, $this->detalle, $this->bonificacion);
    }
    /**
     * Funcion que busca si la orden Posee una bonificacion
     */
    protected function bonificacion()
    {
        if ($this->model->cmenlbon?->bon_ordbon) {
            return cmordcom::Orden($this->model->cmenlbon->bon_ordbon)->buscaProducto($this->detalle->ord_produc)->first();
        }
    }
    /**
     * Funcion que calcula el precio de la orden bonificada
     * @return float
     */
    protected function precioBonificado(): float
    {
        return $this->multiplicaCantidadPrecio(
            $this->bonificacion?->ord_cantid,
            $this->bonificacion?->ord_preuni
        );
    }
    /**
     * Funcion que calcula la cantidad de la orden bonificada
     * @return float
     */
    protected function cantidadBonificada(): float
    {
        return !is_null($this->bonificacion?->ord_cantid) ? $this->bonificacion?->ord_cantid : 0;
    }
    /**
     * Funcion que calcula la cantidad de la orden bonificada
     * @return float
     */
    protected function saldoBonificado(): float
    {
        return !is_null($this->bonificacion?->ord_saldos) ? $this->bonificacion?->ord_saldos : 0;
    }
    /**
     * Funcion que calcula el precio de la orden 
     * @return float
     */
    protected function precio(): float
    {
        return $this->multiplicaCantidadPrecio(
            $this->detalle->ord_cantid,
            $this->detalle->ord_preuni != 0 ? $this->detalle->ord_preuni : $this->detalle->cmproductos->pro_cosmed
        );
    }
    /**
     * Funcion que devuelve la cantidad de la orden
     * @return float
     */
    protected function cantidad(): float
    {
        return round($this->detalle->ord_cantid, 2);
    }
    /**
     * Funcion que devuelve la cantidad de la orden
     * @return float
     */
    protected function saldo(): float
    {
        return round($this->detalle->ord_saldos, 2);
    }
    /**
     * Funcion que suma el precio de la orden mas el precio de la orden bonificada 
     * se divide por la cantidad de las 2 ordenes y 
     * se multiplica el valor de la moneda
     * @return float
     */
    protected function precioCalculado(): float
    {
        return Descuentos::get($this->model, $this->detalle, (($this->precio + $this->precioBonificado) / $this->cantidadCalculada) * $this->valorMoneda);
    }
    /**
     * Funcion que suma la cantidad de la orden por la cantidad de la orden bonificada
     * @return float
     */
    protected function cantidadCalculada(): float
    {
        return round($this->detalle->ord_cantid + $this->cantidadBonificada, 2);
    }
    /**
     * Funcion que suma la cantidad de la orden por la cantidad de la orden bonificada
     * @return float
     */
    protected function saldoCalculado(): float
    {
        return round($this->detalle->ord_saldos + $this->saldoBonificado, 2);
    }
    /**
     * Funcion que multiplica la Cantidad por el precio
     * @return float
     */
    protected function multiplicaCantidadPrecio($cantidad = 0, $precio = 0): float
    {
        return round($cantidad * $precio, 2);
    }
    /**
     * Funcion que calcula el TOTAL en base a la moneda si es el dolar se multiplica segun el precio del dolar en el dia
     * @return float
     */
    protected function valorMoneda(): float
    {
        if ($this->model->ord_moneda != 1) {
            if ($this->model->cmenlmon) return $this->model->cmenlmon?->enl_moneda;
            if ($this->model->tablaparam) return $this->model->tablaparam?->par_dolar;
        }
        return 1;
    }
}
