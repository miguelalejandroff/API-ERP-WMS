<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cmbodega extends Model
{
    use HasFactory;

    protected $table = 'cmbodega';

    protected $primaryKey = 'bod_codigo';

    public $incrementing = false;

    public $timestamps = false;

    public function ScopeBodega($query, $bodega = null)
    {
        return $query->where('bod_codigo', $bodega)->first();
    }
}
