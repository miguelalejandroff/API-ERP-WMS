<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class grupos extends Model
{
    use HasFactory;
    protected $table = 'grupos';

    protected $connection = 'informix';

    protected $primaryKey = 'cod_rg';

    public $incrementing = false;

    public $timestamps = false;
}
