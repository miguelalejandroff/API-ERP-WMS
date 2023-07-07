<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class guicompra extends Model
{
    use HasFactory;
    
    protected $table = 'guicompra';

    protected $primaryKey = 'gui_clave';

    public $incrementing = true;

    public $timestamps = false;

    protected $fillable = [
        "gui_numero",
        "gui_tipgui",
        "gui_fechag",
        "gui_ordcom",
        "gui_numrut",
        "gui_digrut",
        "gui_subcta",
        "gui_nombre",
        "gui_guipro",
        "gui_facpro",
        "gui_facals",
        "gui_sucori",
        "gui_sucdes",
        "gui_paract",
        "gui_fecmod",
        "gui_codusu",
        "gui_empres",
        "gui_current"
    ];
}
