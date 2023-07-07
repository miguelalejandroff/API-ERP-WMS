<?php

namespace App\Models\mongodb;

use Jenssegers\Mongodb\Eloquent\Model;

class Saldos extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'saldos';

    protected $fillable = [
        "codigoBodega",
        "codigoProducto",
        "periodoId",
        "existencia",
        "saldoInicial",
        "saldoEntrada",
        "saldoLogico",
        "saldoFisico",
        "saldoSalida",
        "saldoFinal",
    ];
}
