<?php

namespace App\WMS\Templates;

use App\WMS\Build\Templates;
use App\WMS\Traits\ValidationTrait;

class OrdenEntradaDetalle extends Templates
{
    use ValidationTrait;

    public function __construct(
        protected string $codDeposito,
        protected string $nroOrdenEntrada,
        protected string $nroLinea,
        protected string $codItem,
        protected ?int $codMoneda,
        protected float $cantidadSolicitada,
        protected array|Item $item
    ) {
        $this->notNullNotEmpty($this->codMoneda, 1);
    }
}
