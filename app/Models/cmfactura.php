<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cmfactura extends Model
{
    use HasFactory, HasCompositePrimaryKey;

    protected $table = 'cmfactura';

    protected $primaryKey = ['fac_nrodoc', 'fac_tipdoc'];

    protected $connection = 'informix';

    public $incrementing = false;

    public $timestamps = false;


    public function ScopeNotaCredito($query, $notaCredito = null)
    {
        return $query->where('fac_nrodoc', $notaCredito)->first();
    }

    public function cmfacdet()
    {
        return $this->hasMany(cmfacdet::class, 'fac_nrodoc', 'fac_nrodoc');
    }
    public function cmclientes()
    {
        return $this->hasOne(cmclientes::class, 'aux_claves', 'fac_subcta');
    }
}
