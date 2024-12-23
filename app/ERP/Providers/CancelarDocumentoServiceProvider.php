<?php

namespace App\ERP\Providers;

use App\Http\Controllers\ApiController;
use App\ERP\Context\OrdenEntradaContext;
use App\ERP\Contracts\CancelarDocumentoService;
use App\ERP\Adapters\OrdenEntrada\DespachoTransito;
use App\ERP\Enum\TipoDocumentoERP;
use App\Exceptions\CustomException;
use App\ERP\Adapters\OrdenEntrada\CancelarOrdSaldoAdapter;
use App\ERP\Adapters\OrdenEntrada\InfAjustes;
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
        /**
         * Vincula la interfaz OrdenEntradaService a una función anónima que resuelve la implementación.
         */
        $this->app->bind(CancelarDocumentoService::class, function ($app) {
            $context = new OrdenEntradaContext($app->request->all());
            switch ($context->recepcionWms->GetDocumento('tipoDocumentoERP')) {
                case TipoDocumentoERP::SOLICITUD_RECEPCION->value;
                    return new CancelarOrdSaldoAdapter($context);
                    break;

                //case TipoDocumentoERP::GUIA_DEVOLUCION->value:
                //case TipoDocumentoERP::GUIA_DESPACHO->value:
                //case TipoDocumentoERP::TRASPASO_SUCURSAL->value:
                    //return new InfAjustes($context);
                    //break;


            default:
                throw new CustomException("El tipo de Documento: '{$context->recepcionWms->GetDocumento('tipoDocumentoERP')}' no coincide con ninguna categoría valida en el sistema", [], 500);

            }

            /**
             * Crea un objeto de contexto para mantener los datos relevantes.
             */
            
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
