<?php

namespace App\ERP\Traits;

use App\Models\guicompra;
use Illuminate\Support\Facades\Log;

trait DocumentoTrait
{

    /**
     * @var object $ordenCompra define la orden de compra.
     */
    private $documento;
    /**
     * Devuelve la orden de compra o un valor específico si se proporciona una clave.
     *
     * @param string|null $clave La clave del valor que se desea obtener de la orden.
     * @return mixed La orden de compra completa o el valor de la clave especificada.
     */
    public function getDocumento($clave = null)
    {
        if ($clave === null) {
            return $this->documento;
        }

        if (isset($this->documento->$clave)) {
            return $this->documento->$clave;
        }

        // Opcional: Manejar el caso de que la clave no exista.
        return null;
    }

    public function setDocumento($documento)
    {
        $this->documento = $documento;
    }

    public function guardarDocumento(callable $callback)
    {
        $documento = $this->documento ?? new $this->modeloDocumento;

        $callback($documento);

        $documento->saveOrFail();

        $this->cargarDocumento($documento->{$this->claveDocumento});
    }
}
