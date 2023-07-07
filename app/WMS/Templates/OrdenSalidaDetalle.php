<?php

namespace App\WMS\Templates;

use App\WMS\Build\Templates;

class OrdenSalidaDetalle extends Templates
{
    public function __construct(
        protected string $codDeposito,
        protected string $nroOrdenSalida,
        protected string $nroLinea,
        protected string $pickID,
        protected string $codItem,
        protected ?string $nroLote,
        protected ?string $fechaVencimiento,
        protected ?string $fechaFabricacion,
        protected ?string $nroSerie,
        protected float $cantidad,
        protected ?float $montoCosto,
        protected ?float $montoNeto,
        protected ?float $porcentajeIVA,
        protected ?float $montoIVA,
        protected ?float $porcentajeDescuento,
        protected ?float $montoDescuento,
        protected ?float $montoTotal,
        protected ?string $inventariable,
    ) {
    }
}
