<?php

namespace App\ERP\Context;

use App\ERP\Traits\CargaDocumentoTrait;
use App\ERP\Traits\DetalleTrait;
use App\ERP\Traits\DocumentoTrait;
use App\Models\guicompra;
use App\Models\guidetcompra;

class GuiaCompraContext
{
    use DetalleTrait;
    use DocumentoTrait;
    use CargaDocumentoTrait;

    /**
     * @var string $tipoDocumento define el tipo de guía de compra. Por defecto es "07".
     */
    public $tipoDocumento = "07";

    private $modeloDocumento = guicompra::class;
    private $claveDocumento = 'gui_ordcom';
    public $enviaWms = false;

    private $modeloDocumentoDetalle = guidetcompra::class;

    public function cargarDocumento($documento)
    {
        $this->cargarDocumentoGenerico(function () use ($documento) {
            return $this->modeloDocumento::where('gui_ordcom', $documento)->where('gui_tipgui', $this->tipoDocumento)->first();
        }, 'guidetcompra');
    }
}
