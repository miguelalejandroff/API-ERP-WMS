<?php

namespace App\WMS\Templates;

use App\WMS\Build\Templates;
use App\WMS\Traits\ValidationTrait;

class OrdenEntrada extends Templates
{
    use ValidationTrait;

    public function __construct(
        protected string $codDeposito,
        protected string $nroOrdenEntrada,
        protected ?string $nroReferencia,
        protected ?string $nroOrdenCliente,
        protected string $codTipo,
        protected ?string $codProveedor,
        protected ?string $codCliente,
        protected ?string $codSucursal,
        protected ?int $tipoDeCambio,
        protected ?string $fechaEstimadaRecepcion,
        protected ?string $fechaExpiracion,
        protected ?string $fechaEmisionERP,
        protected ?string $codDepositoOrigen,
        protected ?string $codDepositoOrigen2,
        protected ?string $observacion,
        protected ?string $origen,
        protected ?string $requiereVas,
        protected ?string $esCrossDocking,
        protected ?string $nroCrossDocking,
        protected ?string $codMoneda,
        protected array|OrdenEntradaDetalle $ordenEntradaDetalle,
        protected array|Proveedor $proveedor,
        protected array|Cliente $cliente
    ) {
        $this->notNullNotEmpty($this->nroReferencia, "");
        $this->notNullNotEmpty($this->nroOrdenCliente, "");
        $this->notNullNotEmpty($this->codProveedor, "");
        $this->notNullNotEmpty($this->codCliente, "");
        $this->notNullNotEmpty($this->codSucursal, "");
        $this->notNullNotEmpty($this->tipoDeCambio, 0);
        $this->notNullNotEmpty($this->fechaEstimadaRecepcion, "");
        $this->notNullNotEmpty($this->fechaExpiracion, "");
        $this->notNullNotEmpty($this->fechaEmisionERP, "");
        $this->notNullNotEmpty($this->codDepositoOrigen, "");
        $this->notNullNotEmpty($this->codDepositoOrigen2, "");
        $this->notNullNotEmpty($this->observacion, "");
        $this->notNullNotEmpty($this->origen, "");
        $this->notNullNotEmpty($this->requiereVas, "");
        $this->notNullNotEmpty($this->esCrossDocking, "");
        $this->notNullNotEmpty($this->nroCrossDocking, "");
        $this->notNullNotEmpty($this->codMoneda, "1");
        $this->unsetEmpty($this->proveedor);
        $this->unsetEmpty($this->cliente);
        //if (empty($proveedor)) unset($this->proveedor);
        //if (empty($cliente)) unset($this->cliente);
    }
}
