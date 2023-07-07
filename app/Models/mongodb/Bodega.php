<?php

namespace App\Models\mongodb;

use Jenssegers\Mongodb\Eloquent\Model;

class Bodega extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'bodegas';

    protected $fillable = [
        "codigoBodega",
        "nombreBodega",
    ];
}
