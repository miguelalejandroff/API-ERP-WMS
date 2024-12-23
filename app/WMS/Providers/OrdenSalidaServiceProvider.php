<?php

namespace App\WMS\Providers;

use App\Exceptions\CustomException;
use Illuminate\Support\Facades\App;
use App\Models\despachoencab;
use App\Models\pedidosencabezado;
use App\Events\ActualizarDesdeWMSEvent;
use App\Models\cmguias;
use App\Models\cmfactura;
use Illuminate\Support\Facades\Log;
use App\WMS\Adapters\OrdenSalida\SolicitudDespacho;
use App\WMS\Adapters\OrdenSalida\GuiaDespachoKDX;
use App\WMS\Adapters\OrdenSalida\Ajustesalida;
use App\WMS\Adapters\OrdenSalida\PedidoKDX;
use App\WMS\Adapters\OrdenSalida\Factura;
use App\WMS\Adapters\OrdenSalida\Pedidos;
use App\WMS\Contracts\Outbound\OrdenSalidaService;
use Illuminate\Support\ServiceProvider;

class OrdenSalidaServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(OrdenSalidaService::class, function ($app) {
            $adapter = null;
            $model = null;
            $ultimoDigito = null;

            try {
                $tracking = $app->request->attributes->get('tracking');

                switch (true) {
                    case $app->request->solicitudGuia:
                        $model = despachoencab::SolicitudGuia($app->request->solicitudGuia);
                        $adapter = SolicitudDespacho::class;
                        break;

                    case $app->request->despachoKdx:
                        
                        $despachos = $app->request->despachoKdx;
    
                        // Extraer los últimos dos dígitos del número de ajuste
                        $despachotipo = substr($despachos, -2);
                    
                        // Obtener el número de ajuste sin los últimos dos dígitos
                        $despachorequest = substr($despachos, 0, -2);
                        Log::info('Buscando ajuste con gui_numero: ' . $despachorequest . ', gui_tipgui: ' . $despachotipo);

                        $model = cmguias::where([
                            'gui_numero' => $despachorequest,
                            'gui_tipgui' => $despachotipo
                        ])->first(); 

                        if ($model) {
                            Log::info('Modelo encontrado:', ['model' => $model->toArray()]);
                        } else {
                            Log::error('No se encontró ningún modelo para los valores de ajuste proporcionados.');
                            throw new CustomException('Modelo no encontrado', [], 500);
                        }
                        $adapter = GuiaDespachoKDX::class;
                        break;

                        case $app->request->ajuste:
                            $ajustes = $app->request->ajuste;
    
                            // Extraer los últimos dos dígitos del número de ajuste
                            $ajustetipo = substr($ajustes, -2);
                        
                            // Obtener el número de ajuste sin los últimos dos dígitos
                            $ajusterequest = substr($ajustes, 0, -2);
                            Log::info('Buscando ajuste con gui_numero: ' . $ajusterequest . ', gui_tipgui: ' . $ajustetipo);
    
                            $model = cmguias::where([
                                'gui_numero' => $ajusterequest,
                                'gui_tipgui' => $ajustetipo
                            ])->first(); 
    
                            if ($model) {
                                Log::info('Modelo encontrado:', ['model' => $model->toArray()]);
                            } else {
                                Log::error('No se encontró ningún modelo para los valores de ajuste proporcionados.');
                                throw new CustomException('Modelo no encontrado', [], 500);
                            }
                        $adapter = Ajustesalida::class;
                        break;



                    case $app->request->pedidoKdx:
                        $pedidos = $app->request->pedidoKdx;
    
                        // Extraer los últimos dos dígitos del número de ajuste
                        $pedidotipo = substr($pedidos, -2);
                    
                        // Obtener el número de ajuste sin los últimos dos dígitos
                        $pedidorequest = substr($pedidos, 0, -2);
                        Log::info('Buscando ajuste con gui_numero: ' . $pedidorequest . ', gui_tipgui: ' . $pedidotipo);

                        $model = cmguias::where([
                            'gui_numero' => $pedidorequest,
                            'gui_tipgui' => $pedidotipo
                        ])->first(); 

                        if ($model) {
                            Log::info('Modelo encontrado:', ['model' => $model->toArray()]);
                        } else {
                            Log::error('No se encontró ningún modelo para los valores de ajuste proporcionados.');
                            throw new CustomException('Modelo no encontrado', [], 500);
                        }
                        $adapter = PedidoKDX::class;
                        break;

                    case $app->request->facturaKdx:
                        $facturaKdx = $app->request->facturaKdx;
                        $facdocQuery = floor($facturaKdx / 100);
                        $digitoTipo = $facturaKdx % 100;
                        $model = cmfactura::where([
                            'fac_nrodoc' => $facdocQuery,
                            'fac_tipdoc' => $digitoTipo
                        ])->first();
                        $adapter = Factura::class;
                        break;

                    case $app->request->solicitudPedido:
                        $solicitudPedido = $app->request->solicitudPedido;
                        $pedidosQuery = floor(substr($solicitudPedido, 0, -1));
                        $ultimoDigito = substr($solicitudPedido, -1);
                        Log::info('Buscando por solicitudPedido: ' . $pedidosQuery);
                        Log::info('UltimoDigito: ' . $ultimoDigito);
                        $model = pedidosencabezado::SolicitudPedido($pedidosQuery)->first();
                        Log::info('Resultado de la búsqueda:', ['model' => $model]);
                        if ($model) {
                            Log::info('Modelo encontrado para solicitudPedido: ' . $pedidosQuery);
                            Log::info('Creando adaptador Pedidos...');
                            $adapterClass = Pedidos::class;
                            $adapter = new $adapterClass($model, $ultimoDigito);

                        } else {
                            Log::warning('Pedido no encontrado para solicitud: ' . $solicitudPedido);
                            $adapter = null;
                        }
                        break;

                    default:
                        throw new CustomException('No se proporcionó ningún parámetro válido', [], 500);
                }

                if (!$model) {
                    throw new CustomException('Modelo no encontrado', [], 500);
                }

                $trackingData['errors'] = null;
                $trackingData['status'] = 200;
                $trackingData['message'] = 'OK';
                $tracking->addTrackingData($trackingData);

                if ($app->request->solicitudPedido) {
                    $adapterInstance = new $adapter($model, $ultimoDigito);
                } else {
                    $adapterInstance = new $adapter($model);
                }
                return $adapterInstance;
            } catch (CustomException $e) {
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
