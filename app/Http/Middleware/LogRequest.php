<?php

namespace App\Http\Middleware;

use App\Models\mongodb\Tracking;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LogRequest
{
    public function handle(Request $request, Closure $next)
    {

        $request->attributes->set('url', $request->fullUrl());
        $request->attributes->set('method', $request->method());
        $request->attributes->set('payload', $request->all());

        // Enumeración de tipos basada en el nombre del parámetro.
        $types = [
            'guiaCompra' => '07',
            'guiaRecepcion' => '08',
            'solicitudRecepcion' => '08',
        ];

        // Encuentra el parámetro coincidente en la solicitud.
        $paramName = array_intersect_key($request->all(), $types);

        /*if (empty($paramName)) {
            return $next($request); // o cualquier otra acción.
        }*/

        $paramKey = key($paramName)?? key($request->all());
        $document = $request->input($paramKey);
        $type = $types[$paramKey] ?? 9999;

        $tracking = Tracking::firstOrCreate(
            ['document' => $document, 'type' => $type],
            ['tracking' => []]
        );

        $request->attributes->set('tracking', $tracking);
        
        return $next($request);
    }
}
