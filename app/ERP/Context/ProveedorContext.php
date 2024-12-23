<?php

namespace App\ERP\Context;

use App\ERP\Traits\CargaDocumentoTrait;
use App\ERP\Traits\DetalleTrait;
use App\ERP\Traits\DocumentoTrait;
use App\Models\cmclientes;

class ProveedorContext
{
    use DocumentoTrait;
    use CargaDocumentoTrait;

    private $modeloDocumento = cmclientes::class;
    private $claveDocumento = 'aux_claves';

    public function cargarDocumento($subCuenta)
    {

        $this->cargarDocumentoGenerico(function () use ($subCuenta) {
            return $this->documento = cmclientes::Cliente($subCuenta);
        });
    }
}
