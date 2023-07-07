<?php

namespace App\WMS\Templates;

use App\WMS\Build\Templates;

class OrdenDespacho extends Templates
{
    public function __construct(
        protected string $codDeposito,
        protected string $nroOrdenSalida,
        protected ?string $tipoDespacho,
        protected string $tipoEntrega,
        protected ?string $codCourier,
        protected ?string $nomCourier,
        protected string $contactoDespacho,
        protected ?string $direccionDespacho,
        protected ?string $telefonoDespacho,
        protected ?string $correoDespacho,
        protected ?string $comunaDespacho,
        protected ?string $ciudadDespacho,
        protected ?string $regionDespacho,
        protected ?string $paisDespacho,
        protected ?string $rutaDespacho,
        protected ?string $ventanaDespacho,
        protected ?string $horarioDespacho,
        protected ?string $observacionDespacho,
        protected ?string $fechaCompromisoDespacho,
    ) {
    }
}
