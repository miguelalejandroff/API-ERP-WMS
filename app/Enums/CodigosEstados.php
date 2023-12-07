<?php

namespace App\Enums;

enum SaldoBodegaEnum: string
{
    case AUTORIZADO = 'A';
    case EN_MATRIZ = 'M';
    case POR_DESPACHAR = 'C';
}
