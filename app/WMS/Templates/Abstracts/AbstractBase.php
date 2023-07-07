<?php

namespace App\WMS\Templates\Abstracts;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use stdClass;

abstract class AbstractBase
{
    protected $fields;

    public function __construct(protected $model)
    {
    }

    public function get(): ?stdClass
    {
        $newData = new stdClass();
        $newData->codOwner = $this->codOwner();
        foreach ($this->fields as $field) {
            $data = $this->{$field}($this->model);
            if ($data instanceof Collection and $data->isEmpty()) {
                continue;
            }
            if (!$data) {
                continue;
            }
            $newData->$field = $data;
        }
        return $newData;
    }
    /**
     * Numero opcional que puede referenciar al numero de orden de entrada
     */
    public function codOwner()
    {
        return "CALS";
    }
    protected abstract function getJson(): JsonResponse;
}
