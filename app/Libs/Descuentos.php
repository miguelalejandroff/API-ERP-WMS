<?php

namespace App\Libs;

/**
 * Clase para calcular descuentos.
 */
class Descuentos
{
    /**
     * Calcula un porcentaje de un número.
     *
     * @param float $percentage El porcentaje que se desea calcular.
     * @param float $number El número al que se le aplicará el porcentaje.
     * @param float $rate La tasa del porcentaje (por defecto es 100).
     *
     * @return float El resultado del cálculo.
     */
    protected function calcularPorcentaje($percentage, $number, $rate = 100): float
    {
        return round(($percentage / $number) * $rate, 2);
    }

    /**
     * Calcula un número restando un porcentaje de él.
     *
     * @param float $number El número al que se le restará el porcentaje.
     * @param float $percentage El porcentaje que se restará del número.
     * @param float $rate La tasa del porcentaje (por defecto es 100).
     *
     * @return float El resultado del cálculo.
     */
    protected function restarPorcentaje($number, $percentage, $rate = 100): float
    {
        return round($number - ($number * ($percentage / $rate)), 2);
    }

    /**
     * Calcula el subtotal descontado de una orden.
     *
     * @param mixed $orden El objeto de orden que se utilizará para el cálculo.
     *
     * @return float El subtotal descontado.
     */
    protected function calcularSubtotalDescontado($model): float
    {
        return round((float)$model->ord_netcom - (float)$model->ord_descu1, 2);
    }

    /**
     * Calcula el porcentaje del tercer descuento de la orden.
     *
     * @param mixed $orden El objeto de orden que se utilizará para el cálculo.
     *
     * @return float El porcentaje del tercer descuento.
     */
    protected function calcularDescuentoTres($orden): float
    {
        return round($this->calcularPorcentaje($orden->ord_descu3, ($this->calcularSubtotalDescontado($orden) - $orden->ord_descu2)), 2);
    }

    /**
     * Calcula el porcentaje del segundo descuento de la orden.
     *
     * @param mixed $orden El objeto de orden que se utilizará para el cálculo.
     *
     * @return float El porcentaje del segundo descuento.
     */
    protected function calcularDescuentoDos($orden): float
    {
        return round($this->calcularPorcentaje($orden->ord_descu2, $this->calcularSubtotalDescontado($orden)), 2);
    }

    /**
     * Calcula el descuento para un detalle de la orden.
     *
     * @param mixed $model El modelo de orden.
     * @param mixed $detalle El detalle de la orden.
     * @param float $calculo El cálculo base para el descuento.
     *
     * @return float El descuento para el detalle.
     */
    protected function calcularDescuento($model, $detalle, $calculo): float
    {
        if ($detalle->cmproductos?->enlacepromo) {
            return 1;
        }

        $n1 = round($this->restarPorcentaje($calculo, $this->calcularDescuentoDos($model)), 2);

        $n2 = round($this->restarPorcentaje($n1, $this->calcularDescuentoTres($model)), 2);

        $n3 = round($this->restarPorcentaje($n2, $detalle->ord_descue), 2);

        return round($this->restarPorcentaje($n3, $detalle->ord_descue2), 2);
    }

    public static function get($model, $detalle, $calculo, $self = new self)
    {
        return $self->calcularDescuento($model, $detalle, $calculo);
    }
}
