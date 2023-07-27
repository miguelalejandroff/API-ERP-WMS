<?php

namespace App\Models;

use App\Http\Controllers\Examples\Orden;
use App\Http\Controllers\Examples\OrdenDetalle;
use App\Http\Controllers\Examples\Proveedor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\DB;

/**
 * Clase cmordcom
 * Representa una orden de compra en el sistema.
 *
 * @package App\Models
 */
class cmordcom extends Model
{
    use HasFactory;
    
    /**
     * @var string $table El nombre de la tabla asociada con el modelo.
     */
    protected $table = 'cmordcom';
    
    /**
     * @var string $primaryKey La clave primaria para el modelo.
     */
    protected $primaryKey = 'ord_numcom';
    
    /**
     * @var string $connection El nombre de la conexión de la base de datos para el modelo.
     */
    protected $connection = 'informix';
    
    /**
     * @var bool $incrementing Indica si los ID son autoincrementables.
     */
    public $incrementing = false;
    
    /**
     * @var bool $timestamps Indica si el modelo debe tener marcas de tiempo.
     */
    public $timestamps = false;
    
    /**
     * @var array $fillable Los atributos que se pueden asignar masivamente.
     */
    protected $fillable = [
        'ord_estado'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * Detalle de los ítems de la orden.
     */
    public function cmdetord()
    {
        /**
         * @return hasMany La relación hasMany entre cmordcom y cmdetord. 
         * cmdetord contiene los detalles de los items de la orden.
         */
        return $this->hasMany(cmdetord::class, 'ord_numcom', 'ord_numcom');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     * Observaciones que puede tener la orden.
     */
    public function cmordobs()
    {
        /**
         * @return hasOne La relación hasOne entre cmordcom y cmordobs. 
         * cmordobs contiene las observaciones de la orden.
         */
        return $this->hasOne(cmordobs::class, 'ord_numord', 'ord_numcom');
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
        return $this->hasOne(cmclientes::class, 'aux_claves', 'ord_subcta');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * Sucursales donde puede estar la orden.
     */
    public function sucursalpororden()
    {
        /**
         * @return hasMany La relación hasMany entre cmordcom y sucursalpororden. 
         * sucursalpororden contiene las sucursales donde puede estar la orden.
         */
        return $this->hasMany(sucursalpororden::class, 'oc_numero', 'ord_numcom');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     * Si la orden tiene una orden bonificada.
     */
    public function cmenlbon()
    {
        /**
         * @return hasOne La relación hasOne entre cmordcom y cmenlbon. 
         * cmenlbon representa si la orden tiene una orden bonificada.
         */
        return $this->hasOne(cmenlbon::class, 'bon_ordori', 'ord_numcom');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     * Si una orden bonificada tiene una orden normal.
     */
    public function cmenlori()
    {
        /**
         * @return hasOne La relación hasOne entre cmordcom y cmenlori. 
         * cmenlori representa si una orden bonificada tiene una orden normal.
         */
        return $this->hasOne(cmenlbon::class, 'bon_ordbon', 'ord_numcom');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     * Hace referencia al valor del dólar del día que se generó la orden.
     */
    public function cmenlmon()
    {
        /**
         * @return hasOne La relación hasOne entre cmordcom y cmenlmon. 
         * cmenlmon representa el valor del dólar el día que se generó la orden.
         */
        return $this->hasOne(cmenlmon::class, 'enl_ordcom', 'ord_numcom');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     * Hace referencia al valor del dólar del día, visto por fecha.
     */
    public function tablaparam()
    {
        /**
         * @return hasOne La relación hasOne entre cmordcom y tablaparam. 
         * tablaparam representa el valor del dólar el día que se generó la orden, visto por fecha.
         */
        return $this->hasOne(tablaparam::class, 'par_fecha', 'ord_fechac');
    }


    /**
     * @param $query
     * @param $producto
     * @return mixed
     * Busca un producto en los detalles de la orden.
     */
    public function ScopebuscaProducto($query, $producto)
    {
        /**
         * @param $query La instancia del constructor de consultas Eloquent.
         * @param $producto El producto a buscar.
         * @return mixed La primera instancia del modelo cmdetord donde 'ord_produc' es igual a $producto.
         */
        return $this->cmdetord->filter(function ($item) use ($producto) {
            return $item->ord_produc == $producto;
        });
    }


    /**
     * @param $query
     * @param null $orden
     * @return mixed
     * Busca una orden en particular.
     */
    public function ScopeOrden($query, $orden = null)
    {
        /**
         * @param $query La instancia del constructor de consultas Eloquent.
         * @param $orden La orden a buscar.
         * @return mixed La primera instancia del modelo cmordcom donde 'ord_numcom' es igual a $orden.
         */
        return $query->where('ord_numcom', $orden)->first();
    }
}
