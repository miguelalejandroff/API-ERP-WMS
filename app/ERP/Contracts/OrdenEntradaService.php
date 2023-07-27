<?php

namespace App\ERP\Contracts;

use Illuminate\Http\Request;

interface OrdenEntradaService
{

    public function __construct($context);

    public function run();
}
