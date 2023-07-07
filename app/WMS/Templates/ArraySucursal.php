<?php

namespace App\WMS\Templates;

use App\WMS\Build\Templates;
use App\WMS\Traits\ValidationTrait;

class ArraySucursal extends Templates
{
    use ValidationTrait;

    public function __construct(
        protected string $codCliente,
        protected string $codSucursal,
        protected string $nomSucursal,
        protected ?string $direccion,
        protected ?string $comuna,
        protected ?string $ciudad,
        protected ?string $region,
        protected ?string $localidad,
        protected ?string $pais,
        protected ?int $codTipo,
    ) {
        $this->notNullNotEmpty($this->direccion, "");
        $this->notNullNotEmpty($this->comuna, "");
        $this->notNullNotEmpty($this->ciudad, "");
        $this->notNullNotEmpty($this->region, "");
        $this->notNullNotEmpty($this->localidad, "");
        $this->notNullNotEmpty($this->pais, "");
        $this->notNullNotEmpty($this->codTipo, 1);
    }
}
