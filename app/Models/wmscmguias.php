<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class wmscmguias extends Model
{
    use HasFactory;
    protected $table = 'wmscmguias';

    public $timestamps = false;

    protected $fillable = [
        'gui_numero',
        'gui_tipgui',
        'gui_fechag',
        'gui_ordcom',
        'gui_numrut',
        'gui_digrut',
        'gui_subcta',
        'gui_nombre',
        'gui_guipro',
        'gui_facpro',
        'gui_facals',
        'gui_sucori',
        'gui_sucdes',
        'gui_paract',
        'gui_fecmod',
        'gui_codusu',
        'gui_empres',
    ];
}
