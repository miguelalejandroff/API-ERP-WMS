<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cmbodega extends Model
{
    use HasFactory;

    protected $table = 'cmbodega';

    protected $primaryKey = 'bod_codigo';

    protected $connection = 'informix';

    public $incrementing = false;

    public $timestamps = false;

    public function ScopeBodega($query, $bodega = null)
    {
        return $query->where('bod_codigo', $bodega)->first();
    }

    public function ScopeBodegaSucursal($query, $sucursal = null, $division = 'M')
    {
        return $query->where('bod_codsuc', $sucursal)->where('bod_divisi', $division)->first(); //Obtiene la primera consulta de la bodega y obtiene sucursal
    }
}
