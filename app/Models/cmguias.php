<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cmguias extends Model
{
    use HasFactory, HasCompositePrimaryKey;

    protected $table = 'cmguias';

    protected $primaryKey = ['gui_numero', 'gui_tipgui'];

    protected $connection = 'informix';

    public $incrementing = false;

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
    ];

    public function cmdetgui()
    {
        return $this->hasMany(cmdetgui::class, 'gui_numero', 'gui_numero');
    }

    public function ScopeOrden($query, $orden = null)
    {
        return $query->where('gui_numero', $orden)->first();
    }
}
