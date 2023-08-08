<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;

class CustomException extends Exception
{
    private $trackingData;
    private $request;

    public function __construct($message, $errors = [], $code = 0, Exception $previous = null)
    {

        $this->request = Request::instance();

        $this->trackingData = [
            'url' => $this->request->attributes->get('url'),
            'method' => $this->request->attributes->get('method'),
            'payload' => $this->request->attributes->get('payload'),
            'message' => $message,
            'errors' => $errors,
            'status' => $code
        ];

        parent::__construct($message, $code, $previous);
    }

    public function getTrackingData()
    {
        return $this->trackingData;
    }
    public function render()
    {
        return response()->json($this->getTrackingData(), $this->getCode());
    }
    public function saveToDatabase()
    {
        $tracking = $this->request->attributes->get('tracking');
        $tracking->addTrackingData($this->trackingData);
    }
}
