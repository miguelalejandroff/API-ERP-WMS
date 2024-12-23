<?php

namespace App\Console\Commands;

use App\Jobs\ActualizarMaestroProductoWMS;
use App\Libs\WMS;
use App\Models\cmproductos;
use App\Models\mongodb\Tracking;
use App\Services\RequestHandler;
use App\WMS\Adapters\Admin\CreateItem;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ActualizarMaestroProductos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'actualizar:maestro-productos {--limit=200}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualiza el maestro de productos en el sistema WMS';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $limit = (int)$this->option('limit');
        $trackingCount = 9000;

        $this->info("Iniciando actualización del maestro de productos con un límite de {$limit} por chunk.");

        try {
            cmproductos::where('pro_anomes', Carbon::now()->format('Ym'))
                ->where('pro_estado', 'A')
                ->orderBy('pro_codigo')
                ->chunk($limit, function ($items) use (&$trackingCount) {
                    $array = [];
                    $trackingDocument = Carbon::now()->format('Ym');
                    $tracking = Tracking::firstOrCreate(
                        ['document' => $trackingDocument, 'type' => $trackingCount++],
                        ['tracking' => []]
                    );

                    foreach ($items as $itemData) {
                        try {
                            $model = cmproductos::sku($itemData->pro_codigo);
                            $array[] = (new CreateItem($model))->get();
                            Log::info("Producto procesado: {$itemData->pro_codigo}");
                        } catch (\Exception $e) {
                            Log::error("Error procesando producto {$itemData->pro_codigo}: {$e->getMessage()}");
                        }
                    }

                    if (!empty($array)) {
                        try {
                            $response = WMS::post('WMS_Admin/CreateItem', [
                                'codOwner' => "CALS",
                                'item' => $array,
                            ]);

                            Log::info("Productos enviados al servicio WMS. Respuesta: " . json_encode($response));
                        } catch (\Exception $e) {
                            Log::critical("Error al enviar productos al servicio WMS: {$e->getMessage()}");
                        }
                    }
                });

            $this->info("Actualización del maestro de productos completada.");
            return Command::SUCCESS;
        } catch (\Exception $e) {
            Log::critical("Error crítico en la ejecución del comando: {$e->getMessage()}");
            $this->error("Ocurrió un error crítico. Revisa los logs para más detalles.");
            return Command::FAILURE;
        }
    }
}
