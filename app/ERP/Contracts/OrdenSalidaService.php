<?php

namespace App\ERP\Contracts;

use Illuminate\Http\Request;

interface OrdenSalidaService
{

    public function __construct($context);

    public function run();
}
