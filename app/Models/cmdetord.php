<?php

namespace App\Models;

use App\Casts\CalculaCostoCast;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cmdetord extends Model
{
    use HasFactory, HasCompositePrimaryKey;

    protected $table = 'cmdetord';

    protected $primaryKey = ['ord_numcom', 'ord_produc'];

    protected $connection = 'informix';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'ord_numcom',
        "ord_produc",
        "ord_descri",
        "ord_unimed",
        "ord_cantid",
        "ord_preuni",
        "ord_descue",
        "ord_descue2",
        "ord_saldos",
    ];

    protected $casts = [
        'calculaCosto' => CalculaCostoCast::class,
    ]; 
    // referencia pedidosdetalles

    public function cmordcom()
    {
        return $this->belongsTo(cmordcom::class, 'ord_numcom', 'ord_numcom');
    }
    public function cmproductos()
    {
        return $this->hasOne(cmproductos::class, 'pro_codigo', 'ord_produc')->where('pro_anomes', now()->format('Ym'));
    }

}
