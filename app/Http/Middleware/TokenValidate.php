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

        // Verifica si la solicitud es interna comprobando la dirección IP
        if ($this->isRequestInternal($request)) {
            return $next($request);
        }

        if ($request->header('dataAuth') !== env('token_validate')) {
            return response()->json([
                'message' => 'Token incorrecto'
            ], 500);
        }

        return $next($request);
    }


    private function isRequestInternal(Request $request): bool
    {
        // Comprobar si la solicitud proviene de la misma máquina
        // Esto se puede ajustar según la configuración de tu servidor
        return $request->server('REMOTE_ADDR') === '198.1.1.122';
    }
}
