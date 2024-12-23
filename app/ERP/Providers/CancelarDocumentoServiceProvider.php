<?php

namespace App\ERP\Providers;

use App\ERP\Context\OrdenEntradaContext;
use App\ERP\Contracts\CancelarDocumentoService;
use App\ERP\Adapters\OrdenEntrada\CancelarOrdSaldoAdapter;
use App\ERP\Adapters\OrdenEntrada\InfAjustes;
use App\ERP\Enum\TipoDocumentoERP;
use App\Exceptions\CustomException;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;

class CancelarDocumentoServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(CancelarDocumentoService::class, function ($app) {
            // Crear y validar el contexto
            $context = $this->crearContexto($app);

            // Mapeo de tipos de documento a clases de adaptadores
            $mapaAdaptadores = [
                TipoDocumentoERP::SOLICITUD_RECEPCION->value => CancelarOrdSaldoAdapter::class,
                TipoDocumentoERP::GUIA_DEVOLUCION->value => InfAjustes::class,
                TipoDocumentoERP::GUIA_DESPACHO->value => InfAjustes::class,
                TipoDocumentoERP::TRASPASO_SUCURSAL->value => InfAjustes::class,
            ];

            $tipoDocumento = $context->recepcionWms->GetDocumento('tipoDocumentoERP');

            if (!isset($mapaAdaptadores[$tipoDocumento])) {
                $this->lanzarExcepcionDocumentoNoValido($tipoDocumento);
            }

            Log::info('CancelarDocumentoService', ['tipoDocumento' => $tipoDocumento]);

            // Instanciar el adaptador correspondiente
            return new $mapaAdaptadores[$tipoDocumento]($context);
        });
    }

    /**
     * Crea el contexto validado.
     *
     * @param mixed $app
     * @return OrdenEntradaContext
     */
    private function crearContexto($app)
    {
        $request = $app->request;

        // Validar y filtrar los datos relevantes
        $datosFiltrados = $request->only([
            'tipoDocumentoERP',
            'numeroDocumento',
            'detalles',
        ]);

        return new OrdenEntradaContext($datosFiltrados);
    }

    /**
     * Lanza una excepción cuando el tipo de documento no es válido.
     *
     * @param string $tipoDocumento
     * @throws CustomException
     */
    private function lanzarExcepcionDocumentoNoValido($tipoDocumento)
    {
        $mensaje = "El tipo de documento '{$tipoDocumento}' no coincide con ninguna categoría válida.";
        Log::error('CancelarDocumentoService', ['error' => $mensaje]);
        throw new CustomException($mensaje, [], 500);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
    }
}
