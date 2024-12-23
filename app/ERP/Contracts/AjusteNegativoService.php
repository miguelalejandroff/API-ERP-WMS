<?php

namespace App\ERP\Contracts;


interface AjusteNegativoService
{

    public function __construct($context);

    public function run();
}
