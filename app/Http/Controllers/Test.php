<?php

namespace App\Http\Controllers;

use App\WMS\Contracts\WMSOrdenEntradaService;

class Test extends Controller
{
    public function createOrdenEntrada(WMSOrdenEntradaService $orden)
    {
        dd($orden->get());
    }
}
