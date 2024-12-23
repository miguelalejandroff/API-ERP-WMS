<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class respuestasguiaswms extends Model
{
    use HasFactory, HasCompositePrimaryKey;

    protected $table = 'respuesta_guias_wms';

    protected $primaryKey = 'gui_numero';

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
        "gui_fechag",
        "gui_canord",
        "gui_canrep",
        "gui_saldos",
        "gui_estado"
    ];
}
