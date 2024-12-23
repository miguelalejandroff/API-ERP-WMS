<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class despachoencab extends Model
{
    use HasFactory, HasCompositePrimaryKey;

    protected $table = 'despacho_encab';

    protected $primaryKey = ['des_folio', 'des_tipo', 'des_sucori', 'des_sucdes'];

    protected $connection = 'informix';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        "des_folio",
        "des_tipo",
        "des_marcaitem",
        "des_fecha",
        "des_numrut",
        "des_digrut",
        "des_subcta",
        "des_nombre",
        "des_guipro",
        "des_facpro",
        "des_facals",
        "des_sucori",
        "des_sucdes",
        "des_estado",
        "des_desestado",
        "des_numgui",
        "des_usuario",
        "des_current"
    ];

    public function despachodetalle()
    {
        return $this->hasMany(despachodetalle::class, 'des_folio', 'des_folio');
    }

    public function cmclientes()
    {
        return $this->hasOne(cmclientes::class, 'aux_claves', 'des_subcta');
    }

    public function ScopeSolicitudGuia($query, $solicitud = null)
    {
        return $query->where('des_folio', $solicitud)->first();
    }
}
