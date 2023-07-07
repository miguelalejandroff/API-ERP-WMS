<?php

namespace App\WMS\Templates;

use App\WMS\Build\Templates;

class OrdenSalidaOMS extends Templates
{
    public function __construct(
        protected string $codDeposito,
        protected string $nroOrdenSalida,
        protected string $nroReferenciaOMS,
        protected string $nombreOMS,
    ) {
    }
}
