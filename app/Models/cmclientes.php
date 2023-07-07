<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cmclientes extends Model
{
    use HasFactory;

    protected $table = 'cmclientes';

    protected $primaryKey = 'aux_claves';

    public $incrementing = false;

    public $timestamps = false;

    public function ScopeCliente($query, $cliente = null)
    {
        return $query->where("aux_claves", $cliente)->first();
    }
}
