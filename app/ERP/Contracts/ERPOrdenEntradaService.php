<?php

namespace App\ERP\Contracts;

use Illuminate\Http\Request;

interface ERPOrdenEntradaService
{

    public function __construct(Request $request);

    public function run($recepcion, $recepcionDetalle, $trackingId);
}
