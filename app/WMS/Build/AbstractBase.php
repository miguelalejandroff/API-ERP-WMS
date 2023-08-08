<?php

namespace App\WMS\Build;

use App\Exceptions\CustomException;
use Exception;
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

            try {
                $data = $this->{$field}($this->model);
            } catch (Exception $e) {
                /*throw new CustomException("Error en el método $field: " . $e->getMessage(), $e->getCode(), $e);*/
                $exception = new CustomException("Error en el parametro: $field", [
                    'message' => $e->getMessage(),
                    'code' => $e->getCode()
                ], 500);

                $exception->saveToDatabase(); // Asumiendo que tienes este método en tu clase CustomException

                throw $exception;
            }

            if (($data instanceof Collection and $data->filter(fn ($item) => !is_null($item))->isEmpty())
                or is_null($data) or (is_array($data) and count($data) == 0)
            ) {
                continue;
            }

            $newData->$field = $data;
        }
        unset($this->model);
        return $newData;
    }
    protected abstract function getJson(): JsonResponse;
}
