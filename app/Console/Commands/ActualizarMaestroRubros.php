<?php

namespace App\Console\Commands;

use App\Models\rubros;
use App\Services\RequestHandler;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ActualizarMaestroRubros extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'actualizar:maestro-rubros {--chunk=200 : Cantidad de registros a procesar por lote}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualiza el maestro de rubros y los sincroniza con un servicio externo.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $chunkSize = (int)$this->option('chunk');
        $requestHandler = app(RequestHandler::class);

        $this->info("Iniciando sincronización de rubros con un tamaño de chunk: {$chunkSize}");

        try {
            rubros::chunk($chunkSize, function ($items) use ($requestHandler) {
                foreach ($items as $itemData) {
                    try {
                        // Validar datos del rubro antes de procesarlo
                        if (empty($itemData->cod_rubro)) {
                            Log::warning("Registro ignorado por falta de código de rubro: " . json_encode($itemData));
                            continue;
                        }

                        // Log para el rubro procesado
                        Log::info("Procesando rubro: {$itemData->cod_rubro}");

                        // Enviar solicitud
                        $response = $requestHandler->sendRequest('CreateItemClase', [
                            'rubro' => "{$itemData->cod_rubro}",
                        ]);

                        // Log opcional para la respuesta
                        Log::info("Respuesta del servicio para rubro {$itemData->cod_rubro}: " . json_encode($response));
                    } catch (\Exception $e) {
                        Log::error("Error procesando rubro {$itemData->cod_rubro}: {$e->getMessage()}");
                    }
                }
            });

            $this->info("Sincronización completada exitosamente.");
            return Command::SUCCESS;
        } catch (\Exception $e) {
            Log::critical("Error crítico en la ejecución del comando: {$e->getMessage()}");
            $this->error("Ocurrió un error crítico. Revisa los logs para más detalles.");
            return Command::FAILURE;
        }
    }
}
