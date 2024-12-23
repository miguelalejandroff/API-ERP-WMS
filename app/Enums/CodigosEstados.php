<?php

namespace App\Enums;

enum PedidosEnum: string
{
    case AUTORIZADO = 'A';
    case EN_MATRIZ = 'M';
    case NO_AUTORIZADO = 'N';
    case POR_DESPACHAR = 'C';
    case DESPACHADO = 'D';
}
