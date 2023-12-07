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
        if ($request->header('dataAuth') !== 'LWHXftRvCLqhW+IiQnHygDMOX2JHZv/KA387nvwqKijhrj3ehMg5VMXx+jT1GPRp') {
            return response()->json([
                'message' => 'Token incorrecto'
            ], 500);
        }

        return $next($request);
    }
}
