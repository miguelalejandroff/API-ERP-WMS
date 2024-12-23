<?php

namespace App\Models;
use App\Casts\CorrelativoRecepcionCast;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class wmscmdetgui extends Model
{
    use HasFactory, HasCompositePrimaryKey;

    protected $table = 'wmscmdetgui';

    protected $primaryKey = ['gui_numero', 'gui_tipgui',  'gui_produc'];

    public $timestamps = false;

    protected $connection = 'informix';

    protected $casts = [
            
        'correlativoRecepcion' => CorrelativoRecepcionCast::class,
    ]
    ;

    protected $fillable = [
        'gui_numero',
        'gui_tipgui',
        'gui_bodori',
        'gui_boddes',
        'gui_produc',
        'gui_descri',
        'gui_canord',
        'gui_canrep',
        'gui_preuni',
    ];

    public function cmproductos()
    {
        return $this->hasOne(cmproductos::class, 'pro_codigo', 'gui_produc')->where('pro_anomes', now()->format('Ym'));
    }
}
