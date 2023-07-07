<?php

namespace App\WMS\Templates;

use App\WMS\Build\Templates;

class OrdenFacturacion extends Templates
{
    public function __construct(
        protected string $codDeposito,
        protected string $nroOrdenSalida,
        protected int $folioFacturacion,
        protected string $tipoFacturacion,
        protected string $direccionFacturacion,
        protected string $telefonoFacturacion,
        protected string $correoFacturacion,
        protected string $comunaFacturacion,
        protected string $ciudadFacturacion,
        protected string $regionFacturacion,
        protected string $paisFacturacion,
        protected string $giroFacturacion,
        protected string $observacionFacturacion,
    ) {
    }
}
