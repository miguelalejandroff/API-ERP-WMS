<?php

namespace App\WMS\Build;

class Templates
{
    protected $codOwner = "CALS";
    public function json()
    {
        return get_object_vars($this);
    }

    public function __get($property)
    {
        if (method_exists($this, $property)) {
            return $this->$property($this->model);
        }
        return "error la propiedad {$property} no existe en el objeto";
    }
    public function __set($property, $value)
    {
        if (property_exists($this, $property)) {
            if (is_null($value)) return;
            $this->$property = $value;
        }
        return "error la propiedad {$property} no existe en el objeto set";
    }
}
