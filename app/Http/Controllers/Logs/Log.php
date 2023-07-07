<?php

namespace App\Http\Controllers\Logs;

use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class Log
{
    public static function append($file, $message)
    {
        $time = Carbon::now();
        Storage::disk('local')->append("/logs/{$file}.log", "[$time] $message");
    }
}
