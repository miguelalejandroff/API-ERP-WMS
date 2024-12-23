<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\WMSController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GetStockDisponible extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:confirmar';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envía la confirmación del stock disponible al sistema WMS';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $this->info("Iniciando confirmación de stock disponible...");

            // Instanciar el controlador y llamar al método correspondiente
            $controller = new WMSController();
            $response = $controller->getStockDisponible(new Request());

            // Registrar respuesta y mostrar mensaje en consola
            Log::info("Confirmación de stock enviada. Respuesta: " . json_encode($response));
            $this->info("Confirmación de stock enviada exitosamente.");

            return Command::SUCCESS;
        } catch (\Exception $e) {
            Log::error("Error al confirmar stock disponible: {$e->getMessage()}");
            $this->error("Ocurrió un error durante la confirmación del stock. Revisa los logs para más detalles.");
            return Command::FAILURE;
        }
    }
}
