<?php

namespace App\WMS\Templates;

use App\WMS\Build\Templates;

class OrdenSalida extends Templates
{
    public function __construct(
        protected string $codDeposito,
        protected string $nroOrdenSalida,
        protected ?string $nroOrdenCliente,
        protected int $tipoOrdenSalida,
        protected ?string $nroReferencia,
        /*protected ?string $nroReferencia2,*/
        /*protected ?string $nroReferenciaMKP,*/
        protected ?int $codMoneda,
        protected string $codCliente,
        protected ?string $codSucursal,
        protected ?string $fechaEmisionERP,
        protected ?string $fechaCompromiso,
        protected ?string $observacion,
        protected ?string $prioridad,
        /*protected ?string $packingList,*/
        /*protected ?string $esCrossDocking,*/
        /*protected ?string $nroCrossDocking,*/
        /*protected ?string $origen,*/
        protected string $codDepositoDespacho,
        protected string $codDepositoDespacho2,
        protected array | OrdenDespacho $ordenDespacho,
        /*protected array | OrdenFacturacion $ordenFacturacion,*/
        protected array | OrdenSalidaDetalle $ordenSalidaDetalle,
        protected array | Cliente $cliente,
        /*protected array | OrdenSalidaOMS $ordenSalidaOMS,*/
    ) {

        if (empty($cliente)) unset($this->cliente);
    }
}
