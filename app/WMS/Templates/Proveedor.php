<?php

namespace App\WMS\Templates;

use App\WMS\Build\Templates;
use App\WMS\Traits\ValidationTrait;

class Proveedor extends Templates
{
    use ValidationTrait;

    public function __construct(
        protected string $codProveedor,
        protected string $rutProveedor,
        protected string $nomProveedor,
        protected ?string $razonSocial,
        protected ?string $nomCorto,
        protected ?string $giroProveedor,
        protected ?string $direccion,
        protected ?string $comuna,
        protected ?string $ciudad,
        protected ?string $pais,
        protected ?string $localidad,
        protected ?string $telefono,
        protected ?string $eMail,
        protected ?string $contacto,
        protected ?int $fillRate
    ) {
        $this->notNullNotEmpty($this->razonSocial, "");
        $this->notNullNotEmpty($this->nomCorto, "");
        $this->notNullNotEmpty($this->giroProveedor, "");
        $this->notNullNotEmpty($this->direccion, "");
        $this->notNullNotEmpty($this->comuna, "");
        $this->notNullNotEmpty($this->ciudad, "");
        $this->notNullNotEmpty($this->pais, "CHILE");
        $this->notNullNotEmpty($this->localidad, "");
        $this->notNullNotEmpty($this->telefono, "");
        $this->notNullNotEmpty($this->eMail, "");
        $this->notNullNotEmpty($this->contacto, "");
        $this->notNullNotEmpty($this->fillRate, 0);
        /* if (is_null($razonSocial) && empty($razonSocial)) $this->razonSocial = "";
        if (is_null($nomCorto) && empty($nomCorto)) $this->nomCorto = "";
        if (is_null($giroProveedor) && empty($giroProveedor)) $this->giroProveedor = "";
        if (is_null($direccion) && empty($direccion)) $this->direccion = "";
        if (is_null($comuna) && empty($comuna)) $this->comuna = "";
        if (is_null($ciudad) && empty($ciudad)) $this->ciudad = "";
        if (is_null($pais) && empty($pais)) $this->pais = "CHILE";
        if (is_null($localidad) && empty($localidad)) $this->localidad = "";
        if (is_null($telefono) && empty($telefono)) $this->telefono = "";
        if (is_null($eMail) && empty($eMail)) $this->eMail = "";
        if (is_null($contacto) && empty($contacto)) $this->contacto = "";
        if (is_null($fillRate) && empty($fillRate)) $this->fillRate = 0;*/
    }
}
