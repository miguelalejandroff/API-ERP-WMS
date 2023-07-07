<?php

namespace  App\WMS\Traits;

trait ValidationTrait
{

    public function notNullNotEmpty(&$variable, $valor)
    {
        if (is_null($variable) && empty($variable)) $variable = $valor;
    }
    public function unsetEmpty(&$variable)
    {
        if (empty($variable)) unset($variable);
    }
}
