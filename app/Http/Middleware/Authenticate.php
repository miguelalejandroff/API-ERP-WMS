<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\JsonResponse;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        // Verificar si la solicitud espera una respuesta JSON
        if (!$request->expectsJson()) {
            // Personaliza la redirección dependiendo del prefijo de la ruta
            if ($request->is('admin/*')) {
                return route('admin.login');
            }

            return route('login'); // Ruta por defecto
        }
    }

    /**
     * Override unauthenticated response for JSON requests.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $guards
     * @return \Illuminate\Http\JsonResponse
     */
    protected function unauthenticated($request, array $guards)
    {
        // Retornar una respuesta de error clara en formato JSON
        return new JsonResponse([
            'error' => 'No autenticado',
            'message' => 'Debes estar autenticado para acceder a este recurso.',
            'code' => 401,
        ], 401);
    }
}
