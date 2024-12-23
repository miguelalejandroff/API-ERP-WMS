<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class despachodetalle extends Model
{
    use HasFactory, HasCompositePrimaryKey;

    protected $table = 'despacho_detalle';

    protected $primaryKey = ['des_folio', 'des_tipo', 'des_codigo'];

    protected $connection = 'informix';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        "des_folio",
        "des_tipo",
        "des_fecha",
        "des_bodori",
        "des_boddes",
        "des_codigo",
        "des_newcod",
        "des_descri",
        "des_unimed",
        "des_stockp",
        "des_preuni",
        "des_msgsuc",
        "des_msggte",
        "des_estado",
        "des_rubro",
        "des_fecaut",
        "des_numgui",
        "des_usuaut "
    ];

    public function cmproductos()
    {
        return $this->hasOne(cmproductos::class, 'pro_codigo', 'des_codigo')->where('pro_anomes', now()->format('Ym'));
    }
}
