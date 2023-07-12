<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class guicompra extends Model
{
    use HasFactory;

    protected $table = 'guicompra';

    protected $primaryKey = 'gui_clave';

    protected $connection = 'informix';

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

    public function guidetcompra()
    {
        return $this->hasMany(guidetcompra::class, 'gui_clave', 'gui_clave');
    }

    public function cmclientes()
    {
        return $this->hasOne(cmclientes::class, 'aux_claves', 'gui_subcta');
    }
    
    public function ScopeOrden($query, $orden = null)
    {
        return $query->where('gui_numero', $orden)->first();
    }
}
