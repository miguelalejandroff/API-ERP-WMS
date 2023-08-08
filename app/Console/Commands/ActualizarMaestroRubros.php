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
    protected $signature = 'actualizar:maestro-rubros';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $requestHandler = app(RequestHandler::class);
        rubros::chunk(200, function ($items) use ($requestHandler) {
            foreach ($items as $itemData) {
                Log::info("rubro: {$itemData->cod_rubro}");
                $requestHandler->sendRequest('CreateItemClase', ['rubro' => "{$itemData->cod_rubro}"]);
            }
        });
    }
}
