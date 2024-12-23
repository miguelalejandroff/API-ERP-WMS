<?php

namespace App\ERP\Providers;

use App\ERP\Adapters\OrdenEntrada\SolicitudRecepcion;
use App\ERP\Adapters\OrdenEntrada\DespachoTransito;
use App\ERP\Context\OrdenEntradaContext;
use App\ERP\Contracts\OrdenEntradaService;
use App\ERP\Enum\OrdenStatus;
use App\ERP\Enum\TipoDocumentoERP;
use App\Exceptions\CustomException;
use App\Models\cmclientes;
use App\Models\cmordcom;
use App\Models\wmscmguias;
use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\DeleteDespachoEnTransito;
use Illuminate\Support\Facades\DB;

class OrdenEntradaServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(OrdenEntradaService::class, function ($app) {
            Log::info('OrdenEntradaService context created:', [
                'request_data' => $app->request->all()
            ]);

            $context = new OrdenEntradaContext($app->request->all()); 
    
            switch ($context->recepcionWms->getDocumento('tipoDocumentoERP')) {
                case TipoDocumentoERP::SOLICITUD_RECEPCION->value:

                    switch ($context->ordenCompra->getDocumento('ord_estado')) {
                        case OrdenStatus::ANULADA->value:
                            throw new CustomException("Orden de Compra Anulada: {$context->ordenCompra->getDocumento('ord_numcom')}", [], 500);
                        case OrdenStatus::CERRADA->value:
                            throw new CustomException("Orden de Compra Cerrada: {$context->ordenCompra->getDocumento('ord_numcom')}", [], 500);
                        case OrdenStatus::PENDIENTE->value or OrdenStatus::RECIBIDA->value:
            
                            $context->cargarDatosSolicitudRecepcion();
                
                            return new SolicitudRecepcion($context);

                            break;
                    }
                

    
                case TipoDocumentoERP::GUIA_DEVOLUCION->value:
                case TipoDocumentoERP::GUIA_DESPACHO->value:
                case TipoDocumentoERP::TRASPASO_SUCURSAL->value:
                    // Agrega la lógica para el caso de GUIA_DEVOLUCION aquí
                    $context->guiaRecepcion = (object) $app->request->all();
        
                    Log::info('Request Logged:', [
                        'context' => $context,
                    ]);
                    return new DespachoTransito($context);
    
                default:
                    throw new CustomException("El tipo de Documento: '{$context->recepcionWms->getDocumento('tipoDocumentoERP')}' no coincide con ninguna categoría válida en el sistema.", [], 500);
            }
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
