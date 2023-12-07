<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RequestHandler
{
    /*
    public function sendRequest(string $parameter, string $value)
    {
        try {
            // Construye la URL con el parámetro dado
            $url = "http://198.1.1.122:8081/WMS/CreateItemClase?{$parameter}={$value}";

            // Define el encabezado dataAuth
            $headers = [
                'dataAuth' => 'HyQeUL1SUrJ5H+Da6zcFzq006RU5AJGr8hul/QcM6xURShqoS8Tt+Znjd7lc55bbVePq2NN0FErHaDCmdHY65w==',
            ];

            // Realiza una solicitud POST al endpoint con el encabezado personalizado
            $response = Http::withHeaders($headers)->post($url);

            // Opcional: puedes verificar el estado de la respuesta
            if ($response->failed()) {
                Log::error("Error procesando {$parameter}: {$value} - Respuesta: " . $response->body());
            }

            return $response;
        } catch (Exception $e) {
            Log::error("Error procesando {$parameter}: {$value} - " . $e->getMessage());
            return null;
        }
    }*/
    public function sendRequest(string $endpoint, array $parameters)
    {
        try {
            // Construye la cadena de consulta a partir de los parámetros
            $queryString = http_build_query($parameters);

            // Construye la URL completa
            $url = "http://198.1.1.122:8081/WMS/{$endpoint}?{$queryString}";

            // Define el encabezado dataAuth & token actualizado a la fecha por Bastian
            $headers = [
                'dataAuth' => 'LWHXftRvCLqhW+IiQnHygDMOX2JHZv/KA387nvwqKijhrj3ehMg5VMXx+jT1GPRp',
            ];

            // Realiza una solicitud POST al endpoint con el encabezado personalizado
            $response = Http::withHeaders($headers)->post($url);

            // Opcional: puedes verificar el estado de la respuesta
            if ($response->failed()) {
                Log::error("Error en el endpoint {$endpoint} - Respuesta: " . $response->body());
            }
            unset($response);
        } catch (Exception $e) {
            Log::error("Error en el endpoint {$endpoint} - " . $e->getMessage());
            return null;
        }
    }
}
