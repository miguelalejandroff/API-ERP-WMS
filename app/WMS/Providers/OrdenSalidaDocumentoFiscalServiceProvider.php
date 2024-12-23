<?php

namespace App\WMS\Providers;

use App\Exceptions\CustomException;
use App\Models\cmguias;
use App\WMS\Adapters\OrdenSalida\GuiaDespacho;
use App\WMS\Adapters\OrdenSalida\GuiaDespachoPedido;
use App\WMS\Contracts\Outbound\OrdenSalidaDocumentoFiscalService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class OrdenSalidaDocumentoFiscalServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(OrdenSalidaDocumentoFiscalService::class, function ($app) {
            try {

                $tracking = $app->request->attributes->get('tracking');
                //Log::info('OrdenSalidaDocumentoFiscalService: Inicio del servicio. Tracking: ' . json_encode($tracking));

                $adapter = null;

                switch (true) {
                    case $app->request->guiaFiscal:
                        $despachos = $app->request->guiaFiscal;
    
                        // Extraer los últimos dos dígitos del número de despacho
                        $despachotipo = substr($despachos, -2);
                    
                        // Obtener el número de ajuste sin los últimos dos dígitos
                        $despachorequest = substr($despachos, 0, -2);
                        Log::info('Buscando despacho con gui_numero: ' . $despachorequest . ', gui_tipgui: ' . $despachotipo);

                        $model = cmguias::where([
                            'gui_numero' => $despachorequest,
                            'gui_tipgui' => $despachotipo
                        ])->first(); 

                        if ($model) {
                            Log::info('Modelo encontrado:', ['model' => $model->toArray()]);
                        } else {
                            Log::error('No se encontró ningún modelo para los valores de despacho proporcionados.');
                            throw new CustomException('Modelo no encontrado', [], 500);
                        }

                        $adapter = GuiaDespacho::class;

                        break;

                    case $app->request->pedidoFiscal:
                        $guiaDespacho = $app->request->pedidoFiscal;
                        $rubro = substr($guiaDespacho, -1);

                        // Extraer los últimos dos dígitos del número de pedido
                        $despachotipo = substr($guiaDespacho, -3, 2);
                    
                        // Obtener el número de pedido sin los últimos cuatro dígitos
                        $despachorequest = substr($guiaDespacho, 0, -3);
                        Log::info('Buscando pedido con gui_numero: ' . $despachorequest . ', gui_tipgui: ' . $despachotipo);

                        $model = cmguias::where([
                            'gui_numero' => $despachorequest,
                            'gui_tipgui' => $despachotipo
                        ])->first(); 

                        if ($model) {
                            Log::info('Modelo encontrado:', ['model' => $model->toArray()]);
                            
                        } else {
                            Log::error('No se encontró ningún modelo para los valores de pedido proporcionados.');
                            throw new CustomException('Modelo no encontrado', [], 500);
                        }
                            
                        $adapterClass = GuiaDespachoPedido::class;
                        $adapter = new $adapterClass($model, $rubro);

                        break;

                    default:
                        throw new CustomException('No se proporciono ningun parametro valido', [], 500);
                }

                
                $trackingData['errors'] = null;
                $trackingData['status'] = 200;
                $trackingData['message'] = 'OK';

                $tracking->addTrackingData($trackingData);

                if ($app->request->pedidoFiscal) {
                    $adapterInstance = new $adapter($model, $rubro);
                } else {
                    $adapterInstance = new $adapter($model);
                }
                return $adapterInstance;
            } catch (CustomException $e) {
                Log::error('Excepción atrapada: ' . $e->getMessage());
                $e->saveToDatabase();
                throw $e; // Cambia el 400 por el código de estado que corresponda
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
