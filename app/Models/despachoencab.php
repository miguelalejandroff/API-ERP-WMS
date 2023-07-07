<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class despachoencab extends Model
{
    use HasFactory, HasCompositePrimaryKey;

    protected $table = 'despacho_encab';

    protected $primaryKey = ['des_folio', 'des_tipo', 'des_sucori', 'des_sucdes'];

    public $incrementing = false;

    public $timestamps = false;

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
