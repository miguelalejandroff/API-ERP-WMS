<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class rubros extends Model
{
    use HasFactory;
    protected $table = 'rubros';

    protected $connection = 'informix';

    protected $primaryKey = 'cod_rubro';

    public $incrementing = false;

    public $timestamps = false;
}
