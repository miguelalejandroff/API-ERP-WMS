<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RequestHandler
{
    private const BASE_URL = 'http://198.1.1.122:8081/WMS/';
    private const AUTH_HEADER = 'dataAuth';

    /**
     * Envía una solicitud POST al endpoint especificado con parámetros.
     *
     * @param string $endpoint
     * @param array $parameters
     * @return mixed
     */
    public function sendRequest(string $endpoint, array $parameters)
    {
        try {
            $url = $this->buildUrl($endpoint, $parameters);
            $headers = $this->getHeaders();

            Log::info("Enviando solicitud POST a {$url}");

            // Enviar la solicitud POST
            $response = Http::withHeaders($headers)->post($url);

            // Verificar si la respuesta fue exitosa
            if ($response->failed()) {
                return $this->handleError($endpoint, $response);
            }

            Log::info("Respuesta exitosa desde {$endpoint}", ['status' => $response->status()]);
            return $response->json();
        } catch (Exception $e) {
            Log::error("Error en el endpoint {$endpoint}: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Construye la URL con parámetros.
     *
     * @param string $endpoint
     * @param array $parameters
     * @return string
     */
    private function buildUrl(string $endpoint, array $parameters): string
    {
        $queryString = http_build_query($parameters);
        return self::BASE_URL . $endpoint . '?' . $queryString;
    }

    /**
     * Obtiene los encabezados necesarios para la solicitud.
     *
     * @return array
     */
    private function getHeaders(): array
    {
        return [
            self::AUTH_HEADER => env('TOKEN_VALIDATE'),
        ];
    }

    /**
     * Maneja los errores de la respuesta.
     *
     * @param string $endpoint
     * @param \Illuminate\Http\Client\Response $response
     * @return array
     */
    private function handleError(string $endpoint, $response): array
    {
        Log::error("Error en el endpoint {$endpoint}", [
            'status' => $response->status(),
            'response' => $response->body(),
        ]);

        return [
            'error' => true,
            'status' => $response->status(),
            'message' => $response->body(),
        ];
    }
}
