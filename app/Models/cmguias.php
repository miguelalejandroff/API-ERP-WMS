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
        return $this->hasMany(cmdetgui::class, 'gui_numero', 'gui_numero')
                    ->where('gui_tipgui', $this->gui_tipgui); // Filtrar por gui_tipgui del modelo actual
    }
    

    public function bodegaOrigen()
    {
        return $this->hasOne(cmbodega::class, 'bod_codsuc', 'gui_sucori'); 
    }

    public function bodegaDestino()
    {
        return $this->hasOne(cmbodega::class, 'bod_codsuc', 'gui_sucdes');
    }

    public function folioguia()
    {
        return $this->hasOne(despachodetalle::class, 'des_numgui', 'gui_numero'); 
    }

    public function folioPedido()
    {
        return $this->hasOne(pedidosdetalles::class, 'ped_numgui', 'gui_numero'); 
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     * Relación con el proveedor de la orden.
     */
    public function cmclientes()
    {
        /**
         * @return hasOne La relación hasOne entre cmordcom y cmclientes. 
         * cmclientes representa al proveedor de la orden.
         */
        return $this->hasOne(cmclientes::class, 'aux_claves', 'gui_subcta');
    }
    public function ScopeOrden($query, $orden = null)
    {
        return $query->where('gui_numero', $orden)->first();
    }
}
