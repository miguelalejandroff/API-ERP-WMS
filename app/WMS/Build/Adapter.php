<?php

namespace App\WMS\Build;

use App\WMS\Adapters\OrdenEntrada\OrdenCompraRecepcion;
use Illuminate\Support\Facades\App;

class Adapter
{

    public function __construct(
        protected  $model = null,
        protected  $codOwner = "CALS",
        protected  $attributes = []
    ) {
        $this->attributes["codOwner"] = $this->codOwner;
    }
    public function __get($property)
    {
        if (method_exists($this, $property)) {
            return $this->$property($this->model);
        }
        return "error la propiedad {$property} no existe en el objeto";
    }
    public function arrayObject($result)
    {
        if (is_array($result)) {
            foreach ($result as $key => $row) {
                if ($this->methodJsonExist($row)) {
                    $result[$key] = $row->json();
                    continue;
                }
                $result[$key] = $row;
            }
        }
        return $result;
    }
    public function methodJsonExist($class)
    {
        return (is_object($class)) && (method_exists($class, 'json'));
    }
    public function modelExist($model)
    {
        return is_null($model) ? $this->model : $model;
    }
    public function classMethodMake($model)
    {
        foreach (get_class_methods($this) as $key => $row) if (strpos($row, 'make') !== false) {

            $newKey = lcfirst(str_replace(["make"], "", $row));

            $result = App::call([new $this, $row], ['model' => $model]);

            $result = $this->arrayObject($result);

            if ($this->methodJsonExist($result)) {

                $this->attributes[$newKey] = $result->json();
                continue;
            }
            $this->attributes[$newKey] = $result;
        }
    }
    public function findAndUpdate(&$array, $closure)
    {
        $array = json_decode(json_encode($array), true);
        foreach ($array as $key => &$value) {
            if (is_array($value)) {
                $this->findAndUpdate($value, $closure); // Llamada recursiva para arreglos anidados
            } else {
                $newValue = (object) $closure($this->model); // Llamada al closure para obtener el nuevo valor
                foreach ($newValue as $k => $v) {
                    if ($key != $k) continue;
                    $value = $v;
                }
            }
        }
        return $array;
    }
    public function getModel($model = null)
    {

        $model = $this->modelExist($model);
        $this->classMethodMake($model);
        return $this->model;
    }
    public function get($model = null)
    {
        $model = $this->modelExist($model);
        $this->classMethodMake($model);
        return $this->attributes;
    }
}
