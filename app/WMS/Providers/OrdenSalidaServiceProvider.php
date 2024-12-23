<?php

namespace App\WMS\Providers;

use App\Exceptions\CustomException;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use App\Models\despachoencab;
use App\Models\pedidosencabezado;
use App\Models\cmguias;
use App\Models\cmfactura;
use App\WMS\Contracts\Outbound\OrdenSalidaService;
use App\WMS\Adapters\OrdenSalida\{
    SolicitudDespacho,
    GuiaDespachoKDX,
    Ajustesalida,
    PedidoKDX,
    Factura,
    Pedidos
};

class OrdenSalidaServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
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
                        [$model, $adapter] = $this->handleDespachoKdx($app->request->despachoKdx);
                        break;

                    case $app->request->ajuste:
                        [$model, $adapter] = $this->handleAjuste($app->request->ajuste);
                        break;

                    case $app->request->pedidoKdx:
                        [$model, $adapter] = $this->handlePedidoKdx($app->request->pedidoKdx);
                        break;

                    case $app->request->facturaKdx:
                        [$model, $adapter] = $this->handleFacturaKdx($app->request->facturaKdx);
                        break;

                    case $app->request->solicitudPedido:
                        [$model, $adapter, $ultimoDigito] = $this->handleSolicitudPedido($app->request->solicitudPedido);
                        break;

                    default:
                        throw new CustomException('No se proporcionó ningún parámetro válido', [], 500);
                }

                if (!$model) {
                    throw new CustomException('Modelo no encontrado', [], 500);
                }

                $trackingData = [
                    'errors' => null,
                    'status' => 200,
                    'message' => 'OK',
                ];
                $tracking->addTrackingData($trackingData);

                return $this->createAdapterInstance($adapter, $model, $ultimoDigito);
            } catch (CustomException $e) {
                $e->saveToDatabase();
                throw $e;
            }
        });
    }

    /**
     * Handle despachoKdx case.
     */
    private function handleDespachoKdx(string $despachoKdx): array
    {
        $despachotipo = substr($despachoKdx, -2);
        $despachorequest = substr($despachoKdx, 0, -2);

        Log::info('Buscando ajuste con gui_numero: ' . $despachorequest . ', gui_tipgui: ' . $despachotipo);

        $model = cmguias::where([
            'gui_numero' => $despachorequest,
            'gui_tipgui' => $despachotipo,
        ])->first();

        if (!$model) {
            Log::error('No se encontró ningún modelo para los valores de ajuste proporcionados.');
            throw new CustomException('Modelo no encontrado', [], 500);
        }

        Log::info('Modelo encontrado:', ['model' => $model->toArray()]);
        return [$model, GuiaDespachoKDX::class];
    }

    /**
     * Handle ajuste case.
     */
    private function handleAjuste(string $ajuste): array
    {
        $ajustetipo = substr($ajuste, -2);
        $ajusterequest = substr($ajuste, 0, -2);

        Log::info('Buscando ajuste con gui_numero: ' . $ajusterequest . ', gui_tipgui: ' . $ajustetipo);

        $model = cmguias::where([
            'gui_numero' => $ajusterequest,
            'gui_tipgui' => $ajustetipo,
        ])->first();

        if (!$model) {
            Log::error('No se encontró ningún modelo para los valores de ajuste proporcionados.');
            throw new CustomException('Modelo no encontrado', [], 500);
        }

        Log::info('Modelo encontrado:', ['model' => $model->toArray()]);
        return [$model, Ajustesalida::class];
    }

    /**
     * Handle pedidoKdx case.
     */
    private function handlePedidoKdx(string $pedidoKdx): array
    {
        $pedidotipo = substr($pedidoKdx, -2);
        $pedidorequest = substr($pedidoKdx, 0, -2);

        Log::info('Buscando pedido con gui_numero: ' . $pedidorequest . ', gui_tipgui: ' . $pedidotipo);

        $model = cmguias::where([
            'gui_numero' => $pedidorequest,
            'gui_tipgui' => $pedidotipo,
        ])->first();

        if (!$model) {
            Log::error('No se encontró ningún modelo para los valores del pedido proporcionados.');
            throw new CustomException('Modelo no encontrado', [], 500);
        }

        Log::info('Modelo encontrado:', ['model' => $model->toArray()]);
        return [$model, PedidoKDX::class];
    }

    /**
     * Handle facturaKdx case.
     */
    private function handleFacturaKdx(int $facturaKdx): array
    {
        $facdocQuery = floor($facturaKdx / 100);
        $digitoTipo = $facturaKdx % 100;

        $model = cmfactura::where([
            'fac_nrodoc' => $facdocQuery,
            'fac_tipdoc' => $digitoTipo,
        ])->first();

        if (!$model) {
            Log::error('No se encontró ningún modelo para la factura proporcionada.');
            throw new CustomException('Modelo no encontrado', [], 500);
        }

        Log::info('Modelo encontrado:', ['model' => $model->toArray()]);
        return [$model, Factura::class];
    }

    /**
     * Handle solicitudPedido case.
     */
    private function handleSolicitudPedido(string $solicitudPedido): array
    {
        $pedidosQuery = floor(substr($solicitudPedido, 0, -1));
        $ultimoDigito = substr($solicitudPedido, -1);

        Log::info('Buscando por solicitudPedido: ' . $pedidosQuery);

        $model = pedidosencabezado::SolicitudPedido($pedidosQuery)->first();

        if (!$model) {
            Log::warning('Pedido no encontrado para solicitud: ' . $solicitudPedido);
            throw new CustomException('Modelo no encontrado', [], 500);
        }

        Log::info('Modelo encontrado:', ['model' => $model->toArray()]);
        return [$model, Pedidos::class, $ultimoDigito];
    }

    /**
     * Create an instance of the adapter.
     */
    private function createAdapterInstance(string $adapter, $model, ?string $ultimoDigito = null): object
    {
        return $ultimoDigito
            ? new $adapter($model, $ultimoDigito)
            : new $adapter($model);
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
