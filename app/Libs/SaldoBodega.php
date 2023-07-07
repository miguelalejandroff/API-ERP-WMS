<?php

namespace App\Libs;

use App\Enums\SaldoBodegaEnum;
use App\Models\cmsalbod;
use App\Models\vpparsis;
use Carbon\Carbon;
use Closure;
use Exception;

class SaldoBodega
{
    public function __construct($bodega, $producto, $cantidad, SaldoBodegaEnum $enum, Closure $catch)
    {
        try {

            $now = Carbon::now();
            $periodo = $this->periodoActual();
            $fn = $enum->value;
            if ($now->month < 7) {
                if ($now->year != Carbon::createFromFormat('Y-m-d', vpparsis::first()->par_fechas)->year) {
                    $periodo = $this->periodoAnterior();
                }
            }
            $modelo = $this->bodega($bodega, $producto);
            if (!$modelo) {
                $modelo =  $this->createBodega($bodega, $producto);
            }
            $modelo->$fn('bod_stockb', $cantidad);
            $modelo->$fn('bod_stolog', $cantidad);
            
            $modelo->$fn($periodo, $cantidad);
        } catch (Exception $e) {
            $catch($e->getMessage());
        }
    }
    protected function periodoAnterior()
    {
        $month = [
            "bod_salen2",
            "bod_salfe2",
            "bod_salma2",
            "bod_salab2",
            "bod_salmy2",
            "bod_salju2",
        ];
        return $month[Carbon::now()->month - 1];
    }
    protected function periodoActual()
    {
        $month = [
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
        return $month[Carbon::now()->month - 1];
    }
    protected function createBodega($bodega, $producto)
    {
        return cmsalbod::create([
            "bod_ano" => Carbon::now()->year,
            "bod_produc" => $producto,
            "bod_bodega" => $bodega,
            "bod_salini" => 0,
            "bod_stockb" => 0,
            "bod_stolog" => 0,
            "bod_storep" => 0,
            "bod_stomax" => 0,
            "bod_salene" => 0,
            "bod_salfeb" => 0,
            "bod_salmar" => 0,
            "bod_salabr" => 0,
            "bod_salmay" => 0,
            "bod_saljun" => 0,
            "bod_saljul" => 0,
            "bod_salago" => 0,
            "bod_salsep" => 0,
            "bod_saloct" => 0,
            "bod_salnov" => 0,
            "bod_saldic" => 0,
            "bod_salen2" => 0,
            "bod_salfe2" => 0,
            "bod_salma2" => 0,
            "bod_salab2" => 0,
            "bod_salmy2" => 0,
            "bod_salju2" => 0,
        ]);
    }
    protected function bodega($bodega, $producto)
    {
        return cmsalbod::where('bod_bodega', $bodega)->where('bod_produc', $producto)->where('bod_ano', Carbon::now()->year)->first();
    }
    /*
    protected function periodo()
    {
        if (Carbon::now()->month < 7) {
            if (Carbon::now()->year != Carbon::createFromFormat('Y-m-d', vpparsis::first()->par_fechas)->year) {
                return  $this->periodoAnterior();
            }
        }
        return $this->periodoActual();
    }
    protected function quitarSaldo($bodega, $producto, $cantidad)
    {
        $modelo = $this->bodega($bodega, $producto);
        if (!$modelo) {
            $modelo =  $this->createBodega($bodega, $producto);
        }
        $modelo->decrement('bod_stockb', $cantidad);
        $modelo->decrement('bod_stolog', $cantidad);
        $modelo->decrement($this->periodo(), $cantidad);
    }
  public static function quitar($bodega, $producto, $cantidad, $self = new self)
    {
        $self->quitarSaldo($bodega, $producto, round($cantidad, 2));
    }

    protected function agregarSaldo($bodega, $producto, $cantidad)
    {
        $modelo = $this->bodega($bodega, $producto);
        if (!$modelo) {
            $modelo =  $this->createBodega($bodega, $producto);
        }
        $modelo->increment('bod_stockb', $cantidad);
        $modelo->increment('bod_stolog', $cantidad);
        $modelo->increment($this->periodo(), $cantidad);
    }

     public static function agregar($bodega, $producto, $cantidad, $self = new self)
    {
        $self->agregarSaldo($bodega, $producto, round($cantidad, 2));
    }*/
}
