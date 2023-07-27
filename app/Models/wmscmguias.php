<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class wmscmguias extends Model
{
    use HasFactory, HasCompositePrimaryKey;

    protected $table = 'wmscmguias';

    protected $primaryKey = ['gui_numero', 'gui_tipgui'];

    public $timestamps = false;

    protected $connection = 'informix';

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

    public function wmscmdetgui()
    {
        return $this->hasMany(wmscmdetgui::class, 'gui_numero', 'gui_numero');
    }
    public function enlrecpromocion()
    {
        return $this->hasOne(enlrecpromocion::class, 'enl_guirec', 'gui_numero');
    }
    public function cmclientes()
    {
        return $this->hasOne(cmclientes::class, 'aux_claves', 'gui_subcta');
    }

    public function ScopeOrden($query, $orden = null)
    {
        return $query->where('gui_numero', $orden)->where('gui_tipgui', '08')->first();
    }

    /**
     * Este Scope local obtiene una solicitud de recepción y, si tiene asociada una promoción, 
     * concatena los detalles de dicha promoción con los detalles de la solicitud de recepción original.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $ordenId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function ScopesolicitudesPromo($query, $numeroRecepcion)
    {
        /**
         * Buscar la solicitud de recepción basada en la solicitud entrante.
         */
        $solicitudRecepcion = $query->where('gui_numero', $numeroRecepcion)->where('gui_tipgui', '08')->first();

        /**
         * Comprobar si la solicitud de recepción tiene una promoción asociada.
         */
        if ($solicitudRecepcion->enlrecpromocion?->enl_guirec) {

            /**
             * Si existe, buscar la promoción.
             */
            $solicitudRecepcionPromocion = $this->where('gui_numero', $solicitudRecepcion->enlrecpromocion?->enl_guipro)->where('gui_tipgui', '08')->first();
            $solicitudRecepcionPromocion->gui_numero = $solicitudRecepcion->gui_numero;
            /**
             * Concatenar los detalles de la promoción con los detalles de la solicitud de recepción original.
             * Como las colecciones en Laravel son inmutables, necesitamos asignar el resultado de la concatenación
             * de vuelta a la colección original.
             */
            $solicitudRecepcion->wmscmdetgui = $solicitudRecepcion->wmscmdetgui->concat($solicitudRecepcionPromocion->wmscmdetgui);
        }

        return $solicitudRecepcion;
    }
}
