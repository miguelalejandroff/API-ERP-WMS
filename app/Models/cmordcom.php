<?php

namespace App\Models;

use App\Http\Controllers\Examples\Orden;
use App\Http\Controllers\Examples\OrdenDetalle;
use App\Http\Controllers\Examples\Proveedor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class cmordcom extends Model
{
    use HasFactory;

    protected $table = 'cmordcom';

    protected $primaryKey = 'ord_numcom';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'ord_estado'
    ];

    public function cmdetord()
    {
        return $this->hasMany(cmdetord::class, 'ord_numcom', 'ord_numcom');
    }
    public function cmordobs()
    {
        return $this->hasOne(cmordobs::class, 'ord_numord', 'ord_numcom');
    }
    public function cmclientes()
    {
        return $this->hasOne(cmclientes::class, 'aux_claves', 'ord_subcta');
    }
    public function sucursalpororden()
    {
        return $this->hasMany(sucursalpororden::class, 'oc_numero', 'ord_numcom');
    }
    public function cmenlbon()
    {
        return $this->hasOne(cmenlbon::class, 'bon_ordori', 'ord_numcom');
    }
    public function cmenlori()
    {
        return $this->hasOne(cmenlbon::class, 'bon_ordbon', 'ord_numcom');
    }
    public function cmenlmon()
    {
        return $this->hasOne(cmenlmon::class, 'enl_ordcom', 'ord_numcom');
    }
    public function tablaparam()
    {
        return $this->hasOne(tablaparam::class, 'par_fecha', 'ord_fechac');
    }

    public function ScopebuscaProducto($query, $producto)
    {
        return $this->cmdetord->filter(function ($item) use ($producto) {
            return $item->ord_produc == $producto;
        });
    }

    public function ScopeOrden($query, $orden = null)
    {
        return $query->where('ord_numcom', $orden)->first();
    }
}
