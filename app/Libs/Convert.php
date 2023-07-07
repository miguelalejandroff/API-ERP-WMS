<?php

namespace App\Libs;

use App\Models\cmdetord;
use App\Models\cmordcom;

class Convert
{
    protected $array;
    protected $string;
    protected $int;
    protected $collect;

    public static function array($array, $self = new self)
    {
        $self->array = $array;
        return $self;
    }
    public function toObject()
    {
        if ($this->array) {
            return collect(array_map(function ($item) {
                if (is_array($item))
                    return (object) $item;
                return $item;
            }, $this->array));
        }
        if ($this->collect) {
        }
    }
}
