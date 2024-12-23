<?php

namespace App\WMS\Providers;

use App\Exceptions\CustomException;
use App\Models\cmguias;
use App\Models\guicompra;
use App\Models\wmscmguias;
use App\Models\cmfactura;
use App\WMS\Adapters\OrdenEntrada\{
    GuiaCompra,
    GuiaDespacho,
    GuiaRecepcion,
    SolicitudRecepcion,
    Ajustes,
    NotaCredito
};
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
    public function register(): void
    {
        $this->app->bind(OrdenEntradaService::class, function ($app) {
            try {
                $tracking = $app->request->attributes->get('tracking');
                Log::info('OrdenEntradaService: Inicio del servicio. Tracking: ' . json_encode($tracking));

                $adapter = null;
                $model = null;

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
                        [$model, $adapter] = $this->handleAjuste($app->request->ajuste);
                        break;

                    case $app->request->solicitudRecepcion:
                        $model = wmscmguias::solicitudesPromo($app->request->solicitudRecepcion);
                        $adapter = SolicitudRecepcion::class;
                        break;

                    case $app->request->guiaDespacho:
                        [$model, $adapter] = $this->handleGuiaDespacho($app->request->guiaDespacho);
                        break;

                    case $app->request->facturaKdx:
                        [$model, $adapter] = $this->handleFacturaKdx($app->request->facturaKdx);
                        break;

                    default:
                        Log::error('OrdenEntradaService: No se proporcionó ningún parámetro válido');
                        throw new CustomException('No se proporcionó ningún parámetro válido', [], 500);
                }

                if (!$model) {
                    throw new CustomException('Modelo no encontrado', [], 500);
                }

                return new $adapter($model);
            } catch (CustomException $e) {
                Log::error('Error en OrdenEntradaService: ' . $e->getMessage());
                $e->saveToDatabase();
                throw $e;
            }
        });
    }

    /**
     * Maneja la lógica para ajustes.
     *
     * @param string $ajuste
     * @return array
     */
    private function handleAjuste(string $ajuste): array
    {
        $ajustetipo = substr($ajuste, -2);
        $ajusterequest = substr($ajuste, 0, -2);

        Log::info("Buscando ajuste con gui_numero: $ajusterequest, gui_tipgui: $ajustetipo");

        $model = cmguias::where([
            'gui_numero' => $ajusterequest,
            'gui_tipgui' => $ajustetipo,
        ])->first();

        if (!$model) {
            Log::error('No se encontró ningún modelo para los valores de ajuste proporcionados.');
            throw new CustomException('Modelo no encontrado', [], 500);
        }

        Log::info('Modelo encontrado:', ['model' => $model->toArray()]);
        return [$model, Ajustes::class];
    }

    /**
     * Maneja la lógica para guias de despacho.
     *
     * @param string $guiaDespacho
     * @return array
     */
    private function handleGuiaDespacho(string $guiaDespacho): array
    {
        $despachotipo = substr($guiaDespacho, -2);
        $despachorequest = substr($guiaDespacho, 0, -2);

        Log::info("Buscando despacho con gui_numero: $despachorequest, gui_tipgui: $despachotipo");

        $model = cmguias::where([
            'gui_numero' => $despachorequest,
            'gui_tipgui' => $despachotipo,
        ])->first();

        if (!$model) {
            Log::error('No se encontró ningún modelo para los valores de despacho proporcionados.');
            throw new CustomException('Modelo no encontrado', [], 500);
        }

        Log::info('Modelo encontrado:', ['model' => $model->toArray()]);
        return [$model, GuiaDespacho::class];
    }

    /**
     * Maneja la lógica para facturas.
     *
     * @param int $facturaKdx
     * @return array
     */
    private function handleFacturaKdx(int $facturaKdx): array
    {
        $facdocQuery = floor($facturaKdx / 100);
        $digitoTipo = $facturaKdx % 100;

        Log::info("Buscando factura con fac_nrodoc: $facdocQuery, fac_tipdoc: $digitoTipo");

        $model = cmfactura::where([
            'fac_nrodoc' => $facdocQuery,
            'fac_tipdoc' => $digitoTipo,
        ])->first();

        if (!$model) {
            Log::error('No se encontró ningún modelo para los valores de factura proporcionados.');
            throw new CustomException('Modelo no encontrado', [], 500);
        }

        Log::info('Modelo encontrado:', ['model' => $model->toArray()]);
        return [$model, NotaCredito::class];
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        //
    }
}
