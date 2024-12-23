<?php

namespace App\ERP\Contracts;

use Illuminate\Http\Request;

interface TraspasoBodegaService
{

    public function __construct($context);

    public function run();
}
