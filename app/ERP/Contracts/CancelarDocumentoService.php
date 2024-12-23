<?php

namespace App\ERP\Contracts;

use Illuminate\Http\Request;

interface CancelarDocumentoService
{

    public function __construct($context);

    public function run();
}
