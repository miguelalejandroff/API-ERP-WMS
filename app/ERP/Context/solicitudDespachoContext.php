<?php

namespace App\ERP\Context;

use App\ERP\Traits\CargaDocumentoTrait;
use App\ERP\Traits\DetalleTrait;
use App\ERP\Traits\DocumentoTrait;
use App\Models\despachoencab;
use App\Models\despachodetalle;

class SolicitudDespachoContext
{
    use DetalleTrait;
    use DocumentoTrait;
    use CargaDocumentoTrait;

    public $tipoDocumento = "05";

    private $modeloDocumento = despachoencab::class;
    private $claveDocumento = 'gui_numero';

    private $modeloDocumentoDetalle = despachodetalle::class;

    public function cargarDocumento($documento)
    {
        $this->cargarDocumentoGenerico(function () use ($documento) {
            return despachoencab::SolicitudGuia($documento);
        }, 'despachodetalle');
    }
}
