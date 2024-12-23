<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WMSStocksDisponibles extends Model
{
    use HasFactory;

    protected $table = 'wmsstocksdisponibles';

    protected $connection = 'informix';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = ['bodega', 'codigo', 'cantidad', 'fecha'];
}
