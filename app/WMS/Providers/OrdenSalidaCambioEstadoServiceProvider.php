<?php

namespace App\WMS\Providers;

use App\WMS\Adapters\OrdenSalida\CambioEstado;
use App\WMS\Contracts\Outbound\OrdenSalidaCambioEstadoService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;

class OrdenSalidaCambioEstadoServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(OrdenSalidaCambioEstadoService::class, function ($app) {
            // Asegúrate de que la solicitud esté disponible
            $request = $app->make(Request::class);

            // Verificar si el parámetro 'CambioEstado' está presente en la solicitud
            if ($request->has('cambioEstado')) {
                // Obtener la cadena de 'CambioEstado'
                $cambioEstado = $request->input('cambioEstado');

                // Dividir la cadena en partes usando el guión como delimitador
                $partes = explode('-', $cambioEstado);

                // Verificar si la cadena tiene el formato correcto
                if (count($partes) === 3) {
                    // Asignar las partes a las variables correspondientes
                    $numeroDocumento = $partes[0];
                    $tipoDocumento = $partes[1];
                    $bodega = $partes[2];

                    // Crear y devolver la instancia de CreateCambioEstado
                    return new CambioEstado($numeroDocumento, $tipoDocumento, $bodega);
                }

                throw new \Exception('La cadena "CambioEstado" no tiene el formato correcto');
            }

            throw new \Exception('El parámetro "CambioEstado" no está presente en la solicitud');
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Aquí puedes agregar cualquier lógica que necesites ejecutar durante el arranque de los servicios
    }
}
