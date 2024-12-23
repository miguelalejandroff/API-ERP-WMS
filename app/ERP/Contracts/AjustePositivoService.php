<?php

namespace App\ERP\Contracts;


interface AjustePositivoService
{

    public function __construct($context);

    public function run();
}
