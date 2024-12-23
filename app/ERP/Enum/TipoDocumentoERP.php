<?php

namespace App\ERP\Enum;

enum TipoDocumentoERP: string
{
    case SOLICITUD_RECEPCION = '08';
    case GUIA_COMPRA = '07';
    case GUIA_DESPACHO = '05';
    case TRASPASO_SUCURSAL = '06';
    case GUIA_DEVOLUCION = '11';
    case VENTA_OTRA_SUCURSAL = '48';
    case OTROS_CONCEPTOS = '39';
    case AJUSTE_POSITIVO = '02';
    case AJUSTE_NEGATIVO = '10';
}
