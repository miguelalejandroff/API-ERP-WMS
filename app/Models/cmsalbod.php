<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cmsalbod extends Model
{
    use HasFactory, HasCompositePrimaryKey;

    protected $table = 'cmsalbod';

    protected $primaryKey = ['bod_ano', 'bod_produc', 'bod_bodega'];

    protected $connection = 'informix';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'bod_ano',
        'bod_produc',
        'bod_bodega',
        "bod_salini",
        'bod_stockb',
        "bod_stolog",
        "bod_storep",
        "bod_stomax",
        "bod_salene",
        "bod_salfeb",
        "bod_salmar",
        "bod_salabr",
        "bod_salmay",
        "bod_saljun",
        "bod_saljul",
        "bod_salago",
        "bod_salsep",
        "bod_saloct",
        "bod_salnov",
        "bod_saldic",
        "bod_salen2",
        "bod_salfe2",
        "bod_salma2",
        "bod_salab2",
        "bod_salmy2",
        "bod_salju2"
    ];
}
