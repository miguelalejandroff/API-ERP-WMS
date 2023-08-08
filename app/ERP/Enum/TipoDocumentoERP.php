<?php

namespace App\ERP\Enum;

enum TipoDocumentoERP: string
{
    case SOLICITUD_RECEPCION = '08';
    case GUIA_COMPRA = '07';
    case SOLICITUD_DESPACHO = '05';
}
