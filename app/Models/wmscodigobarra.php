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

    public function enlaceprd()
    {
        return $this->hasOne(enlaceprd::class, 'codigo_antig', 'codigo_antig');
    }
}
