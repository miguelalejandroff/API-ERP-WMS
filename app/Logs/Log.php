<?php

namespace App\Logs;

use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class Log
{

    public static function append($file, $message, $self = new self)
    {
        //   Storage::disk('local')->append("/logs/{$file}.log", "[$self->time] $message");
    }

    public static function path($file, $path = '/logs')
    {
        return "{$path}{$file}.log";
    }
    public static function storage($file, $message, $id = null, $status = 'local.success', $time)
    {
        if (!is_null($id)) {
            $message = "{'trackingId': {$id}, 'message': {$message}}";
        }
        Storage::disk('local')->append(
            static::path($file),
            "[$time] $status $message"
        );
    }
    public static function debug($file, $message, $id = null)
    {
        return static::storage($file, $message, $id, 'local.debug', Carbon::now());
    }
    public static function info($file, $message, $id = null)
    {
        return static::storage($file, $message, $id, 'local.info', Carbon::now());
    }
    public static function warning($file, $message, $id = null)
    {
        return static::storage($file, $message, $id, 'local.warning', Carbon::now());
    }
    public static function error($file, $message, $id = null)
    {
        return static::storage($file, $message, $id, 'local.error',  Carbon::now());
    }
    public static function critical($file, $message, $id = null)
    {
        return static::storage($file, $message, $id, 'local.critical',  Carbon::now());
    }
    public static function alert($file, $message, $id = null)
    {
        return static::storage($file, $message, $id, 'local.alert',  Carbon::now());
    }
}
