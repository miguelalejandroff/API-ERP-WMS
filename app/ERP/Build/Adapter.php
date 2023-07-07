<?php

namespace App\ERP\Build;

use App\Libs\Convert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class Adapter
{

    public function __construct(
        protected Request $request,
    ) {

        $trackingId = uniqid();

        $recepcion = (object)$this->request->all();

        $recepcionDetalle = Convert::array($recepcion->ordenDetalle)->toObject();

        Storage::disk('json')->put("{$trackingId}.json", json_encode($recepcion, JSON_PRETTY_PRINT));
        $orders = Storage::json("/storage/app/json/{$trackingId}.json");

        $this->run($recepcion, $recepcionDetalle, $trackingId);
    }
    public function __get($property)
    {
        if (method_exists($this, $property)) {
            return $this->$property($this->model);
        }
        return "error la propiedad {$property} no existe en el objeto";
    }
}
