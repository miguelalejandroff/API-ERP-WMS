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

    public function cmproductos()
    {
        return $this->hasOne(cmproductos::class, 'pro_codigo', 'des_codigo')->where('pro_anomes', now()->format('Ym'));
    }
}
