<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class sucursalpororden extends Model
{
    use HasFactory;
    protected $table = 'sucursal_por_ordencompra';

    protected $connection = 'informix';

    public $timestamps = false;
}
