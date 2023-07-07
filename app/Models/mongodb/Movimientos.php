<?php

namespace App\Models\mongodb;

use Jenssegers\Mongodb\Eloquent\Model;

class Movimientos extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'movimientos';

    protected $fillable = [
        "codigoBodega",
        "codigoProducto",
        "periodoId",
        "documentoTipo",
        "documentoNumero",
        "documentoFecha",
        "tipoMovimiento",
        "cantidadSolicitada",
        "cantidadRecepcionada",
        "costoUnitario",
        "costoTotal",
        "saldoAnterior"
    ];
}
