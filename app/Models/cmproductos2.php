<?php

namespace App\Models;

use App\Casts\GetRubroAndGroup;
use App\Http\Controllers\Logs\Log;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class cmproductos2 extends Model
{
    use HasFactory, HasCompositePrimaryKey;

    protected $table = 'cmproductos2';

    protected $primaryKey = ['pro_anomes', 'pro_codigo'];

    protected $connection = 'informix';

    public $incrementing = false;

    public $timestamps = false;

    protected $casts = [
        'productoClase' => GetRubroAndGroup::class,
    ];

    protected $fillable = [
        "pro_prevta",
        "pro_cosmed",
        "pro_comein",
        "pro_stockp",
        "pro_sainto",
        "pro_domaal",
        "pro_femaal",
        "pro_comaal",
        "pro_doulco",
        "pro_feulco",
        "pro_coulco",
        "pro_newcod",
        "pro_invent",
        "pro_fecinv",
        "pro_estado",
    ];

    public function enlacewms()
    {
        return $this->hasOne(enlacewms::class, 'codigo_antig', 'pro_codigo');
    }
    public function wmscodigobarra()
    {
        return $this->hasMany(wmscodigobarra::class, 'codigo_antig', 'pro_codigo');
    }

    public function enlacepromo()
    {
        return $this->hasOne(enlacepromo::class, 'codigo_promos', 'pro_codigo');
    }
    public function proImpnac(): Attribute
    {
        return Attribute::make(
            get: fn(string $value): string => $value == "N" ? "NACIONAL" : "IMPORTADO",
        );
    }

    /**
     * Consulta Producto
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return void
     */
    public function scopeSku($query, $sku)
    {

        try {
            $value = $this->nullException($query->where('pro_codigo', $sku)->first());
            //$value->attributes = array_merge($value->attributes, $value->enlacewms->attributes);
            Log::append('createItem', "local.success {$sku}");
            return $value;
        } catch (Exception $e) {
            Log::append('createItem', "local.error {$sku}");
            exit;
        }
    }
    public function scopebyProducto($query, $producto)
    {
        return $query->where('pro_codigo', $producto)->where('pro_anomes', Carbon::now()->format('Ym'))->first();
    }
    public function nullException($value)
    {
        if (is_null($value)) {
            throw new \ErrorException();
        }
        return $value;
    }
}
