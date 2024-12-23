<?php

namespace App\Console\Commands;

use App\Models\grupos;
use App\Services\RequestHandler;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ActualizarMaestroGrupos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'actualizar:maestro-grupos {--chunk=200 : Cantidad de registros a procesar por lote}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Procesa y sincroniza los registros de la tabla grupos con un servicio externo.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $chunkSize = (int)$this->option('chunk');
        $requestHandler = app(RequestHandler::class);

        $this->info("Iniciando sincronización de grupos con un tamaño de chunk: {$chunkSize}");

        try {
            $processedCount = 0;

            grupos::chunk($chunkSize, function ($items) use ($requestHandler, &$processedCount) {
                foreach ($items as $item) {
                    try {
                        // Validar datos antes de procesar
                        if (empty($item->cod_rg)) {
                            Log::warning("Registro ignorado por falta de código: " . json_encode($item));
                            continue;
                        }

                        // Log para debugging
                        Log::info("Procesando grupo: {$item->cod_rg}");

                        // Enviar solicitud
                        $response = $requestHandler->sendRequest('CreateItemClase', [
                            'subrubro' => $item->cod_rg,
                        ]);

                        // Opcional: Log para respuesta del servicio
                        Log::info("Respuesta del servicio para grupo {$item->cod_rg}: " . json_encode($response));

                        $processedCount++;
                    } catch (\Exception $e) {
                        Log::error("Error procesando grupo {$item->cod_rg}: {$e->getMessage()}");
                    }
                }
            });

            $this->info("Sincronización completada. Total de registros procesados: {$processedCount}");
        } catch (\Exception $e) {
            Log::critical("Error crítico en la ejecución del comando: {$e->getMessage()}");
            $this->error("Ocurrió un error crítico. Revisa los logs para más detalles.");
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
