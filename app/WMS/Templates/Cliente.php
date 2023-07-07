<?php

namespace App\WMS\Templates;

use App\WMS\Build\Templates;
use App\WMS\Traits\ValidationTrait;

class Cliente extends Templates
{
    use ValidationTrait;

    public function __construct(
        protected string $codCliente,
        protected string $rutCliente,
        protected string $nomCliente,
        protected ?string $razonSocial,
        protected ?string $nomCorto,
        protected ?string $giroCliente,
        protected ?string $direccion,
        protected ?string $comuna,
        protected ?string $ciudad,
        protected ?string $pais,
        protected ?string $localidad,
        protected ?string $telefono,
        protected ?string $eMail,
        protected ?string $contacto,
        protected ?int $fillRate,
        protected ?int $codTipo,
        protected ?string $b2B,
        protected array | ArraySucursal $arraySucursal,
    ) {
        $this->notNullNotEmpty($this->razonSocial, "");
        $this->notNullNotEmpty($this->nomCorto, "");
        $this->notNullNotEmpty($this->giroCliente, "");
        $this->notNullNotEmpty($this->direccion, "");
        $this->notNullNotEmpty($this->comuna, "");
        $this->notNullNotEmpty($this->ciudad, "");
        $this->notNullNotEmpty($this->pais, "CHILE");
        $this->notNullNotEmpty($this->localidad, "");
        $this->notNullNotEmpty($this->telefono, "");
        $this->notNullNotEmpty($this->eMail, "");
        $this->notNullNotEmpty($this->contacto, "");
        $this->notNullNotEmpty($this->fillRate, 0);
        $this->notNullNotEmpty($this->codTipo, 1);
        $this->notNullNotEmpty($this->b2B, "");
    }
}
