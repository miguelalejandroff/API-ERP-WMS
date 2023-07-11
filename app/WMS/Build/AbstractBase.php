<?php

namespace App\WMS\Build;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use stdClass;

abstract class AbstractBase
{
    protected $fieldsIgnored = [
        'getJson',
        '__construct',
        'get',
    ];

    public function __construct(protected $model)
    {
    }

    /**
     * Numero opcional que puede referenciar al numero de orden de entrada
     */
    public function codOwner()
    {
        return "CALS";
    }

    public function get(): ?stdClass
    {
        $newData = new stdClass();
        $newData->codOwner = $this->codOwner();
        //foreach ($this->fields as $field) {
        foreach (get_class_methods($this) as $field) {
            
            if (in_array($field, $this->fieldsIgnored)) {
                continue;
            }

            $data = $this->{$field}($this->model);

            if (($data instanceof Collection and $data->filter(fn ($item) => !is_null($item))->isEmpty()) or !$data) {
                continue;
            }

            $newData->$field = $data;
        }
        return $newData;
    }

    protected abstract function getJson(): JsonResponse;
}
