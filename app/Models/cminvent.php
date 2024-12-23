<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cminvent extends Model
{
    use HasFactory;
    protected $table = 'cminvent';

    protected $primaryKey = 'inv_numgui';

    protected $connection = 'informix';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        "inv_numgui",
        "inv_bodega",
        "inv_fechai",
        "inv_codusu",
        "inv_sucurs",
        "inv_empres"
    ];

}


