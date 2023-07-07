<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TokenValidate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->header('dataAuth') !== 'HyQeUL1SUrJ5H+Da6zcFzq006RU5AJGr8hul/QcM6xURShqoS8Tt+Znjd7lc55bbVePq2NN0FErHaDCmdHY65w==') {
            return response()->json([
                'message' => 'Token incorrecto'
            ], 500);
        }

        return $next($request);
    }
}
