<?php

namespace App\Libs;

enum TipoDocumentoWMS: string
{
    case GUIA_COMPRA = '07';
    case GUIA_RECEPCION= '08';
    case SOLICITUD_RECEPCION = '15';
    case NOTA_CREDITO = '05';
    case GUIA_DESPACHO = '17';
    case TRASPASO_SUCURSAL = '18';
    case TRASPASO_BODEGA = '19';
    case GUIA_DEVOLUCION = '16';
}
