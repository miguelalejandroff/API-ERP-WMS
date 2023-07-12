<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class guidetcompra extends Model
{
    use HasFactory, HasCompositePrimaryKey;

    protected $table = 'guidetcompra';

    protected $primaryKey = ['gui_clave', 'gui_numero', 'gui_produc'];

    protected $connection = 'informix';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        "gui_clave",
        "gui_numero",
        "gui_tipgui",
        "gui_bodori",
        "gui_boddes",
        "gui_produc",
        "gui_descri",
        "gui_unimed",
        "gui_canord",
        "gui_canrep",
        "gui_preuni",
        "gui_saldo",
    ];
    
    public function cmproductos()
    {
        return $this->hasOne(cmproductos::class, 'pro_codigo', 'gui_produc')->where('pro_anomes', now()->format('Ym'));
    }
}
