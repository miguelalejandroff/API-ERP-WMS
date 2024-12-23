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
    public function register(): void
    {
        $this->app->bind(OrdenSalidaDocumentoFiscalService::class, function ($app) {
            try {
                $tracking = $app->request->attributes->get('tracking');

                $adapter = null;
                $model = null;
                $rubro = null;

                switch (true) {
                    case $app->request->guiaFiscal:
                        [$model, $adapter] = $this->handleGuiaFiscal($app->request->guiaFiscal);
                        break;

                    case $app->request->pedidoFiscal:
                        [$model, $adapter, $rubro] = $this->handlePedidoFiscal($app->request->pedidoFiscal);
                        break;

                    default:
                        throw new CustomException('No se proporcionó ningún parámetro válido', [], 500);
                }

                if (!$model) {
                    throw new CustomException('Modelo no encontrado', [], 500);
                }

                $this->addTrackingData($tracking);

                return $this->createAdapterInstance($adapter, $model, $rubro);
            } catch (CustomException $e) {
                Log::error('Excepción atrapada: ' . $e->getMessage());
                $e->saveToDatabase();
                throw $e;
            }
        });
    }

    /**
     * Maneja la lógica para guiaFiscal.
     *
     * @param string $guiaFiscal
     * @return array
     */
    private function handleGuiaFiscal(string $guiaFiscal): array
    {
        $despachotipo = substr($guiaFiscal, -2);
        $despachorequest = substr($guiaFiscal, 0, -2);

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
     * Maneja la lógica para pedidoFiscal.
     *
     * @param string $pedidoFiscal
     * @return array
     */
    private function handlePedidoFiscal(string $pedidoFiscal): array
    {
        $rubro = substr($pedidoFiscal, -1);
        $despachotipo = substr($pedidoFiscal, -3, 2);
        $despachorequest = substr($pedidoFiscal, 0, -3);

        Log::info("Buscando pedido con gui_numero: $despachorequest, gui_tipgui: $despachotipo");

        $model = cmguias::where([
            'gui_numero' => $despachorequest,
            'gui_tipgui' => $despachotipo,
        ])->first();

        if (!$model) {
            Log::error('No se encontró ningún modelo para los valores de pedido proporcionados.');
            throw new CustomException('Modelo no encontrado', [], 500);
        }

        Log::info('Modelo encontrado:', ['model' => $model->toArray()]);
        return [$model, GuiaDespachoPedido::class, $rubro];
    }

    /**
     * Añade datos de seguimiento.
     *
     * @param object $tracking
     * @return void
     */
    private function addTrackingData(object $tracking): void
    {
        $trackingData = [
            'errors' => null,
            'status' => 200,
            'message' => 'OK',
        ];
        $tracking->addTrackingData($trackingData);
    }

    /**
     * Crea una instancia del adaptador.
     *
     * @param string $adapter
     * @param object $model
     * @param string|null $rubro
     * @return object
     */
    private function createAdapterInstance(string $adapter, object $model, ?string $rubro = null): object
    {
        return $rubro
            ? new $adapter($model, $rubro)
            : new $adapter($model);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        // No se necesita lógica de arranque
    }
}
