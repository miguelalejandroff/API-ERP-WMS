<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cmdetgui extends Model
{
    use HasFactory, HasCompositePrimaryKey;

    protected $table = 'cmdetgui';

    protected $primaryKey = ['gui_numero', 'gui_tipgui',  'gui_produc'];

    protected $connection = 'informix';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        "gui_numero",
        "gui_tipgui",
        "gui_bodori",
        "gui_boddes",
        "gui_produc",
        "gui_descri",
        "gui_canord",
        "gui_canrep",
        "gui_preuni",
    ];
    
    public function cmproductos()
    {
        return $this->hasOne(cmproductos::class, 'pro_codigo', 'gui_produc')->where('pro_anomes', now()->format('Ym'));
    }
}
