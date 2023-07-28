<?php

namespace App\ERP\Exception;

use Illuminate\Support\Facades\Log;
use Exception;
use Throwable;

class GuiaCompraException extends Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        // Aquí puedes agregar código para registrar el error, por ejemplo:
        Log::error('Guia de Compra:', ['error' => $this->getMessage()]);
    }
}