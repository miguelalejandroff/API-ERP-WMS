<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cmfacdet extends Model
{
    use HasFactory, HasCompositePrimaryKey;

    protected $table = 'cmfacdet';

    protected $primaryKey = ['fac_nrodoc', 'fac_tipdoc',  'fac_codpro'];

    protected $connection = 'informix';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        "fac_nrodoc",
        "fac_tipdoc",
        "fac_bodori",
        "fac_codpro",
        "fac_descri",
        "fac_unimed",
        "fac_cantid",
        "fac_preuni",
        "fac_descue",
        "fac_saldos"
    ];

    public function cmproductos()
    {
        return $this->hasOne(cmproductos::class, 'pro_codigo', 'fac_codpro')->where('pro_anomes', now()->format('Ym'));
    }
}
