<?php

namespace App\WMS\Contracts;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use App\WMS\Build\AbstractBase;

abstract class ItemService extends AbstractBase
{
    protected $fields = [
        'codItem',
        'nomItem',
        'codUnidadMedida',
        'codItemAlternativo',
        'nomAlternativo',
        'controlaLote',
        'controlaSerie',
        'controlaExpiracion',
        'controlaFabricacion',
        'controlaVAS',
        'controlaCantidad',
        'codTipo',
        'marca',
        'origen',
        'esPickeable',
        'inspeccion',
        'crossDocking',
        'codItemClase1',
        'nomItemClase1',
        'codItemClase2',
        'nomItemClase2',
        'codItemClase3',
        'nomItemClase3',
        'codItemClase4',
        'nomItemClase4',
        'codItemClase5',
        'nomItemClase5',
        'codItemClase6',
        'nomItemClase6',
        'codItemClase7',
        'nomItemClase7',
        'codItemClase8',
        'nomItemClase8',
        'itemCodigoBarra'
    ];


    /**
     * Representa el Codigo del Producto 
     */
    abstract protected function codItem($model): string;

    /**
     * Representa el Nombre del Producto
     */
    abstract protected function nomItem($model): string;

    /**
     * Codigo de la unidad de medida Asociada al Producto
     */
    public function codUnidadMedida($model): int
    {
        return 1;
    }

    /**
     * Codigo alternativo del Producto
     */
    public function codItemAlternativo($model): ?string
    {
        return null;
    }

    /**
     * Nombre alternativo del Producto
     */
    public function nomAlternativo($model): ?string
    {
        return null;
    }

    /**
     * Flag que indica si el Producto contrala Lote ("S" o "N")
     */
    public function controlaLote($model): ?string
    {
        return "N";
    }

    /**
     * Flag que indica si el Producto contrala Serie ("S" o "N")
     */
    public function controlaSerie($model): ?string
    {
        return "N";
    }

    /**
     * Flag que indica si el Producto contrala Expiracion ("S" o "N")
     */
    public function controlaExpiracion($model): ?string
    {
        return "N";
    }

    /**
     * Flag que indica si el Producto contrala Fabricacion ("S" o "N")
     */
    public function controlaFabricacion($model): ?string
    {
        return "N";
    }

    /**
     * Flag que indica si el Producto contrala VAS ("S" o "N")
     */
    public function controlaVAS($model): ?string
    {
        return "N";
    }

    /**
     * Flag que indica si el Producto contrala Cantidad ("S" o "N")
     */
    public function controlaCantidad($model): ?string
    {
        return "S";
    }

    /**
     * Codigo del tipo de Producto
     */
    public function codTipo($model): ?string
    {
        return 1;
    }

    /**
     * Campo libre para indicar la marca del Producto
     */
    public function marca($model): ?string
    {
        return null;
    }

    /**
     * Campo libre para indicar si el Producto es "NACIONAL" o "IMPORTADO"
     */
    public function origen($model): ?string
    {
        return "NACIONAL";
    }

    /**
     * Flag que indica si el Producto es pickeable ("S", "N")
     */
    public function esPickeable($model): ?string
    {
        return "S";
    }

    /**
     * Flag que indica si el Producto requiere inspeccion ("S", "N")
     */
    public function inspeccion($model): ?string
    {
        return "N";
    }

    /**
     * Flag que indica si el Producto requiere cuarentena ("S", "N")
     */
    public function cuarentena($model): ?string
    {
        return "N";
    }

    /**
     * Flag que indica si el Producto se procesa a traves de crossDocking ("S", "N")
     */
    public function crossDocking($model): ?string
    {
        return "N";
    }

    /**
     * Representa el codigo de Rubro 
     */
    public function codItemClase1($model): ?string
    {
        return null;
    }

    /**
     * Representa el nombre del rubro
     */
    public function nomItemClase1($model): ?string
    {
        return null;
    }

    /**
     * Representa el codigo de sub-rubro
     */
    public function codItemClase2($model): ?string
    {
        return null;
    }

    /**
     * Representa el nombre del sub-rubro
     */
    public function nomItemClase2($model): ?string
    {
        return null;
    }

    /**
     * NO Representa nada 
     */
    public function codItemClase3($model): ?string
    {
        return null;
    }

    /**
     * NO Representa nada 
     */
    public function nomItemClase3($model): ?string
    {
        return null;
    }

    /**
     * NO Representa nada 
     */
    public function codItemClase4($model): ?string
    {
        return null;
    }

    /**
     * NO Representa nada 
     */
    public function nomItemClase4($model): ?string
    {
        return null;
    }

    /**
     * NO Representa nada 
     */
    public function codItemClase5($model): ?string
    {
        return null;
    }

    /**
     * NO Representa nada 
     */
    public function nomItemClase5($model): ?string
    {
        return null;
    }

    /**
     * NO Representa nada 
     */
    public function codItemClase6($model): ?string
    {
        return null;
    }

    /**
     * NO Representa nada 
     */
    public function nomItemClase6($model): ?string
    {
        return null;
    }

    /**
     * NO Representa nada 
     */
    public function codItemClase7($model): ?string
    {
        return null;
    }

    /**
     * NO Representa nada 
     */
    public function nomItemClase7($model): ?string
    {
        return null;
    }

    /**
     * NO Representa nada 
     */
    public function codItemClase8($model): ?string
    {
        return null;
    }

    /**
     * NO Representa nada 
     */
    public function nomItemClase8($model): ?string
    {
        return null;
    }

    /**
     * NO Representa nada 
     */
    public function itemCodigoBarra($model): ?Collection
    {
        return null;
    }

    public function getJson(): JsonResponse
    {
        return response()->json([
            'codOwner' => parent::codOwner(),
            'item' => [
                parent::get()
            ],
        ]);
    }
}
