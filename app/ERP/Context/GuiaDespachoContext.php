<?php

namespace App\ERP\Context;

use App\ERP\Traits\CargaDocumentoTrait;
use App\ERP\Traits\DetalleTrait;
use App\ERP\Traits\DocumentoTrait;
use App\Models\cmguias;
use App\Models\cmdetgui;

class GuiaDespachoContext
{
    use DetalleTrait;
    use DocumentoTrait;
    use CargaDocumentoTrait;

    /**
     * @var string 
     */
    public $tipoDocumento = "05";

    private $modeloDocumento = cmguias::class;
    private $claveDocumento = 'gui_numero';
    public $enviaWms = false;

    private $modeloDocumentoDetalle = cmdetgui::class;

    public function cargarDocumento($documento)
    {
        $this->cargarDocumentoGenerico(function () use ($documento) {
            return $this->modeloDocumento::where('gui_numero', $documento)->where('gui_tipgui', $this->tipoDocumento)->first();
        }, 'cmdetgui');
    }
}
