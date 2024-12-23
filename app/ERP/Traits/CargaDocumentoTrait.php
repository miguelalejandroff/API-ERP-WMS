<?php

namespace App\ERP\Traits;

use Illuminate\Support\Facades\Log;

trait CargaDocumentoTrait
{
    public function cargarDocumentoGenerico(callable $obtenerDocumento, $detalleRelacion = null)
    {
        // Utilizar el callback para obtener el documento
        Log::info('CargaDocumentoTrait', ['message' => 'Inicia cargaDocumentoGenerico']);
        $this->documento = $obtenerDocumento();

        if (!$this->documento) {
            Log::info('CargaDocumentoTrait', ['message' => 'Documento no encontrado']);
            return;
        }

        if ($detalleRelacion && $this->documento->$detalleRelacion && !$this->documento->$detalleRelacion->isEmpty()) {
            $this->documentoDetalle = &$this->documento->$detalleRelacion;
            Log::info('CargaDocumentoTrait', ['message' => 'Fin cargaDocumentoGenerico', 'documento' => $this->documento]);
        }
    }
}
