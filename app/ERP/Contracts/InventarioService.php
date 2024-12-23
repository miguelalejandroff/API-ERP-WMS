<?php

namespace App\ERP\Contracts;


interface InventarioService
{

    public function __construct($context);

    public function run();
}
