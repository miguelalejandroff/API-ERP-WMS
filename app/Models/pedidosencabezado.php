<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class pedidosencabezado extends Model
{
    use HasFactory;
    protected $table = 'pedidos_encabezado';

    protected $connection = 'informix';

    public $incrementing = false;

    public $timestamps = false;
}
