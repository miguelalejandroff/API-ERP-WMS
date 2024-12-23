<?php

namespace App\ERP\Context;

use App\ERP\Traits\CargaDocumentoTrait;
use App\ERP\Traits\DetalleTrait;
use App\ERP\Traits\DocumentoTrait;
use App\Models\wmscmguias;
use App\Models\wmscmdetgui;

class SolicitudRecepcionContext
{
    use DetalleTrait;
    use DocumentoTrait;
    use CargaDocumentoTrait;

    /**
     * @var string $tipoDocumento define el tipo de guía de compra. Por defecto es "07".
     */
    public $tipoDocumento = "08";

    private $modeloDocumento = wmscmguias::class;
    private $claveDocumento = 'gui_numero';
    public $enviaWms = false;

    private $modeloDocumentoDetalle = wmscmdetgui::class;

    public function cargarDocumento($documento)
    {
        $this->cargarDocumentoGenerico(function () use ($documento) {
            return $this->modeloDocumento::where('gui_numero', $documento)->where('gui_tipgui', $this->tipoDocumento)->first();
        }, 'wmscmdetgui');
    }
}