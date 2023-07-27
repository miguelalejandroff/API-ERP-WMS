<?php

namespace App\WMS\Contracts;

use App\WMS\Build\AbstractBase;

abstract class OrdenSalidaDocumentoFiscalService extends AbstractBase
{

    /**
     * Representa el CD, Site o Bodega a la cual se recibira la mercaderia
     */
    abstract protected function codDeposito($model): string;

    /**
     * Corresponde al numero interno, unico en el sistema de origen para identificar el pedido
     */
    abstract protected function nroOrdenSalida($model): string;

    /**
     * Identificador opcional que permite facturar una carga completa asociada a una o varias ordenes de salida o la parcialidad de la orden indicada
     */
    protected function idCarga($model): string
    {
        return 0;
    }

    /**
     * Valor que indica el folio del documento fiscal predefinido por el sistema de origen
     */
    abstract protected function folioFacturacion($model): int;

    /**
     * Corresponde al tipo de facturacion factura(33), factura exenta(34), boleta(39), Guia Despacho(52)
     */
    abstract protected function tipoFacturacion($model): string;

    /**
     * Representa el CD, Site o Bodega a la cual se recibira la mercaderia
     */
    abstract protected function fechaEmision($model): string;
}
