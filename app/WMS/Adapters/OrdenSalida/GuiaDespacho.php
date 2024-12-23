<?php

namespace App\WMS\Adapters\OrdenSalida;

use App\WMS\Contracts\Outbound\OrdenSalidaDocumentoFiscalService;
use App\Libs\WMS;

class GuiaDespacho extends OrdenSalidaDocumentoFiscalService
{
    protected function codDeposito($model): string
    {
        return $model->cmdetgui->first()->gui_bodori;
    }
    protected function nroOrdenSalida($model): string
    {
        return $model->folioguia->des_folio;    
    }
    protected function folioFacturacion($model): int
    {
        return $model->gui_numero;
    }
    protected function tipoFacturacion($model): string
    {
        return 52;
    }
    protected function fechaEmision($model): string
    {
        return WMS::date($model->gui_fechag, 'Y-m-d');
    }
}
