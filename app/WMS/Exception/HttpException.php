<?php

namespace App\WMS\Exception;

use App\Models\mongodb\Tracking;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Request;

class HttpException extends Exception
{
    private $trackingData;

    public function __construct($message, $code = 0, $trackingData = [], Exception $previous = null)
    {
        $this->trackingData = $trackingData;
        parent::__construct($message, $code, $previous);
    }

    public function getTrackingData()
    {
        return $this->trackingData;
    }

    public function saveToDatabase()
    {

        $request = Request::instance();
        $tracking = $request->attributes->get('tracking');
        $tracking->addTrackingData($this->trackingData);
    }
}
