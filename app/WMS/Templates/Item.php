<?php

namespace App\WMS\Templates;

use App\WMS\Build\Templates;
use App\WMS\Traits\ValidationTrait;

class Item extends Templates
{
    use ValidationTrait;

    public function __construct(
        protected string $codItem,
        protected string $nomItem,
        protected ?int $codUnidadMedida,
        protected ?string $codItemAlternativo,
        protected ?string $nomAlternativo,
        protected ?string $controlaLote,
        protected ?string $controlaSerie,
        protected ?string $controlaExpiracion,
        protected ?string $controlaFabricacion,
        protected ?string $controlaVAS,
        protected ?string $controlaCantidad,
        protected ?string $codTipo,
        protected ?string $marca,
        protected ?string $origen,
        protected ?string $esPickeable,
        protected ?string $inspeccion,
        protected ?string $cuarentena,
        protected ?string $crossDocking,
        protected ?string $codItemClase1,
        /*protected ?string $nomItemClase1,*/
        protected ?string $codItemClase2,
        /*protected ?string $nomItemClase2,*/
        protected ?string $codItemClase3,
        /*protected ?string $nomItemClase3,*/
        protected ?string $codItemClase4,
        /*protected ?string $nomItemClase4,*/
        protected ?string $codItemClase5,
        /*protected ?string $nomItemClase5,*/
        protected ?string $codItemClase6,
        /*protected ?string $nomItemClase6,*/
        protected ?string $codItemClase7,
        /*protected ?string $nomItemClase7,*/
        protected ?string $codItemClase8,
        /*protected ?string $nomItemClase8,*/
        protected array | ItemCodigoBarra $itemCodigoBarra
    ) {
        $this->notNullNotEmpty($this->codUnidadMedida, 1);
        $this->notNullNotEmpty($this->controlaLote, "N");
        $this->notNullNotEmpty($this->controlaSerie, "N");
        $this->notNullNotEmpty($this->controlaExpiracion, "N");
        $this->notNullNotEmpty($this->controlaFabricacion, "N");
        $this->notNullNotEmpty($this->controlaVAS, "N");
        $this->notNullNotEmpty($this->controlaCantidad, "S");
        $this->notNullNotEmpty($this->codTipo, "1");
        $this->notNullNotEmpty($this->marca, "");
        $this->notNullNotEmpty($this->origen, "NACIONAL");
        $this->notNullNotEmpty($this->esPickeable, "S");
        $this->notNullNotEmpty($this->inspeccion, "N");
        $this->notNullNotEmpty($this->cuarentena, "N");
        $this->notNullNotEmpty($this->crossDocking, "N");
        $this->notNullNotEmpty($this->codItemClase1, "");
        /*$this->notNullNotEmpty($this->nomItemClase1, "");*/
        $this->notNullNotEmpty($this->codItemClase2, "");
        /*$this->notNullNotEmpty($this->nomItemClase2, "");*/
        $this->notNullNotEmpty($this->codItemClase3, "");
        /*$this->notNullNotEmpty($this->nomItemClase3, "");*/
        $this->notNullNotEmpty($this->codItemClase4, "");
        /*$this->notNullNotEmpty($this->nomItemClase4, "");*/
        $this->notNullNotEmpty($this->codItemClase5, "");
        /*$this->notNullNotEmpty($this->nomItemClase5, "");*/
        $this->notNullNotEmpty($this->codItemClase6, "");
        /*$this->notNullNotEmpty($this->nomItemClase6, "");*/
        $this->notNullNotEmpty($this->codItemClase7, "");
        /*$this->notNullNotEmpty($this->nomItemClase7, "");*/
        $this->notNullNotEmpty($this->codItemClase8, "");
        /*$this->notNullNotEmpty($this->nomItemClase8, "");*/
    }
}
