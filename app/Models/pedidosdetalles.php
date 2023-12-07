<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class pedidosdetalles extends Model
{
    use HasFactory;
    protected $table = 'pedidos_detalles';

    protected $connection = 'informix';

    public $incrementing = false;

    public $timestamps = false;
}
