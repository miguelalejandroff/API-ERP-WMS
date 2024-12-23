<?php

namespace App\ERP\Traits;

use App\Models\guidetcompra;
use Illuminate\Support\Facades\Log;

trait DetalleTrait
{
    /**
     * @var object $ordenCompra define la orden de compra.
     */
    private $documentoDetalle;


    /**
     * Devuelve detalles de la orden o elementos específicos basados en clave y valor.
     *
     * @param string|null $clave La clave para filtrar los detalles.
     * @param mixed $valor El valor asociado a la clave para filtrar.
     * @return Illuminate\Support\Collection|mixed Colección filtrada o elemento específico.
     */
    public function getDetalle($clave = null, $valor = null)
    {
        if ($clave === null) {
            return $this->documentoDetalle;
        }

        // Filtrar la colección basándose en la clave y el valor
        return $this->documentoDetalle->filter(function ($item) use ($clave, $valor) {
            return isset($item[$clave]) && $item[$clave] == $valor;
        })->first();
    }

    public function setDetalle($detalle)
    {
        $this->documentoDetalle = $detalle;
    }

    /**
     * Itera sobre cada detalle de la orden de compra y aplica una función callback.
     *
     * @param callable $callback La función callback a aplicar a cada detalle.
     */
    public function iterarDetalle(callable $callback)
    {
        foreach ($this->documentoDetalle as &$detalle) {
            $callback($detalle);
        }
    }

    public function guardarDetalle(callable $callbackDetalle, $criteriosBusquedad = null)
    {
        // Obtener el modelo del detalle
        $modeloDetalle = $this->modeloDocumentoDetalle;
    
        // Si hay criterios de búsqueda, intentar encontrar el detalle existente
        if (!is_null($criteriosBusquedad)) {
            $detalleExistente = $modeloDetalle::where($criteriosBusquedad)->first();
        }
    
        // Si existe un detalle, actualizarlo; de lo contrario, crear uno nuevo
        if (isset($detalleExistente)) {
            $detalle = $detalleExistente;
        } else {
            $detalle = new $modeloDetalle;
        }
    
        // Aplicar la función de callback al detalle
        $callbackDetalle($detalle);
    
        // Guardar el detalle
        $detalle->saveOrFail();
    }
    
}
