<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class enlacewms extends Model
{
    use HasFactory;
    protected $table = 'enlacewms';

    protected $connection = 'informix';

    public $timestamps = false;

    public function cmproductos()
    {
        return $this->belongsTo(cmproductos::class, 'pro_codigo', 'codigo_antig')->where('pro_anomes', now()->format('Ym'));
    }
}
