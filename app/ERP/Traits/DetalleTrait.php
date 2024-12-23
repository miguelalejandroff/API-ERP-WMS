<?php

namespace App\ERP\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

trait DetalleTrait
{
    /**
     * @var Collection $documentoDetalle
     */
    private $documentoDetalle;

    /**
     * Obtiene detalles de la orden o filtra por clave y valor.
     *
     * @param string|null $clave La clave para filtrar los detalles.
     * @param mixed $valor El valor asociado a la clave para filtrar.
     * @return Collection|mixed
     */
    public function getDetalle($clave = null, $valor = null)
    {
        if (is_null($clave)) {
            return $this->documentoDetalle;
        }

        return $this->documentoDetalle->filter(fn($item) => isset($item[$clave]) && $item[$clave] == $valor)->first();
    }

    /**
     * Configura el detalle de la orden.
     *
     * @param Collection|array $detalle
     * @throws \InvalidArgumentException
     */
    public function setDetalle($detalle)
    {
        if (is_array($detalle)) {
            $detalle = collect($detalle);
        }

        if (!$detalle instanceof Collection) {
            throw new \InvalidArgumentException("El detalle debe ser una instancia de Collection o un array.");
        }

        $this->documentoDetalle = $detalle;
    }

    /**
     * Itera sobre cada detalle y aplica una función callback.
     *
     * @param callable $callback
     */
    public function iterarDetalle(callable $callback)
    {
        $this->documentoDetalle->each($callback);
    }

    /**
     * Guarda un detalle, actualizándolo si existe o creándolo si no.
     *
     * @param callable $callbackDetalle Callback para definir los atributos del detalle.
     * @param array|null $criteriosBusqueda Criterios para buscar el detalle existente.
     * @throws \Throwable
     */
    public function guardarDetalle(callable $callbackDetalle, $criteriosBusqueda = null)
    {
        try {
            /** @var Model $modeloDetalle */
            $modeloDetalle = $this->modeloDocumentoDetalle;

            $detalle = null;

            // Buscar detalle existente si hay criterios de búsqueda
            if (!is_null($criteriosBusqueda)) {
                $detalle = $modeloDetalle::where($criteriosBusqueda)->first();
            }

            // Crear una nueva instancia si no existe
            if (!$detalle) {
                $detalle = new $modeloDetalle;
            }

            // Aplicar callback para asignar valores
            $callbackDetalle($detalle);

            // Guardar el detalle
            $detalle->saveOrFail();

            Log::info("Detalle guardado exitosamente.", ['detalle' => $detalle]);
        } catch (\Exception $e) {
            Log::error("Error al guardar el detalle.", ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}
