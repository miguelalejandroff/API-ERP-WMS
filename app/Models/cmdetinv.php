<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cmdetinv extends Model
{
    use HasFactory;
    protected $table = 'cmdetinv';

    protected $primaryKey = ['inv_numgui', 'inv_produc'];

    protected $connection = 'informix';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        "inv_numgui",
        "inv_produc",
        "inv_descri",
        "inv_cantid"
    ];

}