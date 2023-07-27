<?php

namespace App\ERP\Enum;

enum OrdenStatus: string
{
    case RECIBIDA = 'R';
    case ANULADA = 'A';
    case CERRADA = 'C';
    case PENDIENTE = 'P';
}
