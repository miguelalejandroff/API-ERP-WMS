<?php

namespace App\Models\mongodb;

use Jenssegers\Mongodb\Eloquent\Model;

class Tracking extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'log';


    protected $fillable = [
        'document',
        'type',
        'tracking',
    ];

    protected $casts = [
        'tracking' => 'array',
    ];

    public function addTrackingData($data)
    {
        $currentTracking = $this->tracking ?? [];
        $currentTracking[] = $data;
        $this->tracking = $currentTracking;
        $this->save();
    }
}
