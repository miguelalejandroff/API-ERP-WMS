<?php

namespace App\Models;

use App\Casts\CorrelativoUnicoCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class pedidosdetalles extends Model
{
    use HasFactory;
    protected $table = 'pedidos_detalles';

    protected $connection = 'informix';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        "ped_folio",
        "ped_fecha",
        "ped_codrub",
        "ped_nomrubro",
        "ped_codigo",
        "ped_descri",
        "ped_unimed",
        "ped_vtaant",
        "ped_cantsol",
        "ped_stocksuc",
        "ped_stockmat",
        "ped_stockemp",
        "ped_cantori",
        "ped_fecaut",
        "ped_estped",
        "ped_nomestado",
        "ped_msgsuc"

    ];

        public function pedidosencabezado()
        {
            return $this->belongsTo(pedidosencabezado::class, 'ped_folio', 'ped_folio');
        }
    
}
