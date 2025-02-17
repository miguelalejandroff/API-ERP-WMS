<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class wmscodigobarra extends Model
{
    use HasFactory;
    protected $table = 'wmscodigobarra';

    protected $connection = 'informix';

    public $timestamps = false;

    public function cmproductos()
    {
        return $this->hasOne(cmproductos::class, 'pro_codigo', 'codigo_antig');
    }
}
