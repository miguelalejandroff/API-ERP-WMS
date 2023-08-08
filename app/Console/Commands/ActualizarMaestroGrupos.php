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
    protected $signature = 'actualizar:maestro-grupos';

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
        grupos::chunk(200, function ($items) use ($requestHandler) {
            foreach ($items as $itemData) {
                Log::info("grupo: {$itemData->cod_rg}");
                $requestHandler->sendRequest('CreateItemClase', ['subrubro' => "{$itemData->cod_rg}"]);
            }
        });
    }
}
