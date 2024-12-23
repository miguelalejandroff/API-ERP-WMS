<?php

namespace App\ERP\Context;

use App\ERP\Traits\CargaDocumentoTrait;
use App\ERP\Traits\DetalleTrait;
use App\ERP\Traits\DocumentoTrait;
use App\Models\cmordcom;
use App\Models\cmdetord;

class OrdenCompraContext
{
    use DetalleTrait;
    use DocumentoTrait;
    use CargaDocumentoTrait;
    
    private $modeloDocumento = cmordcom::class;
    private $claveDocumento = 'ord_numcom';

    private $modeloDocumentoDetalle = cmdetord::class;
    /**
     * @var object $ordenCompra define la orden de compra.
     */
    private $documentoBonificado;

    public function cargarDocumento($documento)
    {
        $this->cargarDocumentoGenerico(function () use ($documento) {
            return cmordcom::Orden($documento);
        }, 'cmdetord');
    }
}
