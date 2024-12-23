<?php

namespace App\Libs;

enum TipoDocumentoWMS: string
{
    case GUIA_COMPRA = '7';
    case GUIA_RECEPCION= '8';
    case SOLICITUD_RECEPCION = '15';
    case NOTA_CREDITO = '5';
    case GUIA_DESPACHO = '17';
    case TRASPASO_SUCURSAL = '18';
    case TRASPASO_BODEGA = '19';
    case GUIA_DEVOLUCION = '16';
}
