<?php

namespace App\WMS\Templates;

use App\WMS\Build\Templates;
use App\WMS\Traits\ValidationTrait;

class ItemCodigoBarra extends Templates
{
    use ValidationTrait;

    public function __construct(
        protected ?int $codUnidadMedida,
        protected string $codItem,
        protected string $codigoBarra,
        protected string $alias,
        protected ?float $factor,
        protected ?float $ancho,
        protected ?float $largo,
        protected ?float $alto,
        protected ?float $peso,
        protected ?float $volumen,
        protected ?int $secuencia,
    ) {
        $this->notNullNotEmpty($this->codUnidadMedida, 1);
        $this->notNullNotEmpty($this->factor, 1);
        $this->notNullNotEmpty($this->ancho, 0);
        $this->notNullNotEmpty($this->largo, 0);
        $this->notNullNotEmpty($this->alto, 0);
        $this->notNullNotEmpty($this->peso, 0);
        $this->notNullNotEmpty($this->volumen, 0);
        $this->notNullNotEmpty($this->secuencia, 1);
    }
}
