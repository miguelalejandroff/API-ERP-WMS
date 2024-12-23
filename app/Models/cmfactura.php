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

    protected $fillable = [
        "fac_nrodoc",
        "fac_tipdoc",
        "fac_fecdoc",
        "fac_fecvto",
        "fac_conpag",
        "fac_subcta",
        "fac_numrut",
        "fac_digrut",
        "fac_nombre",
        "fac_direcc",
        "fac_comuna",
        "fac_ciudad",
        "fac_empres",
        "fac_codsuc",
        "fac_codven",
        "fac_codbod",
        "fac_deptos",
        "fac_param",
        "fac_netoc",
        "fac_netov",
        "fac_decto",
        "fac_flete",
        "fac_iva",
        "fac_montot"
    ];


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

    public function regnotac()
    {
        return $this->hasOne(regnotac::class, 'nrocre', 'fac_nrodoc');
    }
}
