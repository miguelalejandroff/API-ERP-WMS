<?php

namespace App\ERP\Traits;

use Illuminate\Support\Facades\Log;
use Exception;

trait CargaDocumentoTrait
{
    /**
     * Carga un documento y su detalle relacionado si existe.
     *
     * @param callable $obtenerDocumento Callback para obtener el documento.
     * @param string|null $detalleRelacion Relación opcional con los detalles del documento.
     * @return bool Devuelve true si se carga el documento correctamente, false si no se encuentra.
     */
    public function cargarDocumentoGenerico(callable $obtenerDocumento, $detalleRelacion = null): bool
    {
        try {
            Log::info('CargaDocumentoTrait - Inicia cargaDocumentoGenerico');

            // Obtener el documento utilizando el callback
            $this->documento = $obtenerDocumento();

            if (!$this->documento) {
                Log::warning('CargaDocumentoTrait - Documento no encontrado');
                return false;
            }

            // Cargar la relación si está definida y no está vacía
            if ($detalleRelacion && method_exists($this->documento, $detalleRelacion)) {
                $detalle = $this->documento->$detalleRelacion;

                if ($detalle && !$detalle->isEmpty()) {
                    $this->documentoDetalle = $detalle;
                    Log::info('CargaDocumentoTrait - Documento cargado con detalle', [
                        'documento' => $this->documento,
                        'detalle' => $detalle
                    ]);
                    return true;
                }
            }

            Log::info('CargaDocumentoTrait - Documento cargado sin detalles relacionados', [
                'documento' => $this->documento
            ]);

            return true;
        } catch (Exception $e) {
            Log::error('CargaDocumentoTrait - Error en cargaDocumentoGenerico', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}
