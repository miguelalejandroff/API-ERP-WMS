<?php

namespace App\ERP\Context;

use App\ERP\Traits\DetalleTrait;
use App\ERP\Traits\DocumentoTrait;
use Carbon\Carbon;

class RecepcionWmsContext
{
    use DetalleTrait;
    use DocumentoTrait;

    public function cargarDocumento($requestData)
    {
        $this->documento = (object)$requestData;

        $this->documento->fechaRecepcionWMS = Carbon::now();
        $this->documentoDetalle = collect($this->documento->documentoDetalle);
    }
}
