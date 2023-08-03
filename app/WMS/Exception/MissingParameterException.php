<?php

namespace App\WMS\Exception;

use Exception;
use Illuminate\Support\Facades\Request;

class MissingParameterException extends Exception
{
    public function __construct()
    {
        parent::__construct('No se proporcionó ningún parámetro válido', 400);
    }
}