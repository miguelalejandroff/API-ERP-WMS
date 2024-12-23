<?php

namespace App\WMS\Providers;

use App\Exceptions\CustomException;
use App\Models\cmguias;
use App\Models\guicompra;
use App\Models\wmscmguias;
use App\Models\cmfactura;
use App\WMS\Adapters\OrdenEntrada\GuiaCompra;
use App\WMS\Adapters\OrdenEntrada\GuiaDespacho;
use App\WMS\Adapters\OrdenEntrada\GuiaRecepcion;
use App\WMS\Adapters\OrdenEntrada\SolicitudRecepcion;
use App\WMS\Adapters\OrdenEntrada\Ajustes;
use App\WMS\Adapters\OrdenEntrada\NotaCredito;
use App\WMS\Contracts\Inbound\OrdenEntradaService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;


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
            try {

                $tracking = $app->request->attributes->get('tracking');
                Log::info('OrdenEntradaService: Inicio del servicio. Tracking: ' . json_encode($tracking));

                $adapter = null;

                switch (true) {
                    case $app->request->guiaCompra:
                        $model = guicompra::Orden($app->request->guiaCompra);

                        $adapter = GuiaCompra::class;
                        break;
                    case $app->request->guiaRecepcion:
                        $model = cmguias::Orden($app->request->guiaRecepcion);
                        $adapter = GuiaRecepcion::class;
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

                        $adapter = Ajustes::class;
                        break;
                    case $app->request->solicitudRecepcion:
                        $model = wmscmguias::solicitudesPromo($app->request->solicitudRecepcion);
                        $adapter = SolicitudRecepcion::class;
                        break;
                    case $app->request->guiaDespacho:
                        $despachos = $app->request->guiaDespacho;
    
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
                            Log::error('No se encontró ningún modelo para los valores de despacho proporcionados.');
                            throw new CustomException('Modelo no encontrado', [], 500);
                        }                        
                        $adapter = GuiaDespacho::class;
                        break;

                    case $app->request->facturaKdx:
                        $facturaKdx = $app->request->facturaKdx;
                        $facdocQuery = floor($facturaKdx / 100);  // Elimina el último dígito
                        $digitoTipo = $facturaKdx % 100;  // Obtiene el último dígito
    
                        $model = cmfactura::where([
                            'fac_nrodoc' => $facdocQuery,
                            'fac_tipdoc' => $digitoTipo
                        ])->first();
    
                        $adapter = NotaCredito::class;
                        break;
                    default:
                        Log::error('OrdenEntradaService: No se proporcionó ningún parámetro válido');
                        throw new CustomException('No se proporciono ningun parametro valido', [], 500);

                }
                if (!$model) {
                    throw new CustomException('Modelo no encontrado', [], 500);
                }
                return new $adapter($model);
            } catch (CustomException $e) {
                $e->saveToDatabase();
                throw $e;
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
