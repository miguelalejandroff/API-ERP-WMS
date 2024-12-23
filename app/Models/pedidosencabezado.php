<?php

namespace App\Models;

use App\Casts\CorrelativoUnicoCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;


class pedidosencabezado extends Model
{
    use HasFactory;
    protected $table = 'pedidos_encabezado';

    protected $connection = 'informix';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        "ped_folio",
        "ped_fecha",
        "ped_codsuc",
        "ped_nomsuc",
        "ped_estado",
        "ped_desestado",
        "ped_usuario",
        "ped_current",
        "ped_tipo"
    ];

    protected $casts = [
            
        'correlativounico' => CorrelativoUnicoCast::class,
    ]
    ;

    public function scopeSolicitudPedido($query, $solicitud = null)
    {
        return $query->where('ped_folio', $solicitud);
    }
    

    public function pedidosdetalles()
    {
        return $this->hasMany(pedidosdetalles::class, 'ped_folio', 'ped_folio');
    }

    public function bodegaOrigen()
    {
        return $this->hasOne(cmbodega::class, 'bod_codsuc', 'ped_codsuc'); 
                    
    }

    protected function setCorrelativounicoAttribute($value)
    {
        Log::info("Setting correlativounico attribute: $value");
        $this->attributes['correlativounico'] = $value;
    }

}
