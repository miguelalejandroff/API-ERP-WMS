<?php

namespace App\Models\mongodb;

use Jenssegers\Mongodb\Eloquent\Model;

class Producto extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'productos';

    protected $fillable = [
        "codigoProducto",
        "nombreProducto",
    ];
}
