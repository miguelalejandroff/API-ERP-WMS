<?php

namespace App\Console\Commands;

use App\Jobs\ActualizarMaestroProductoWMS;
use Illuminate\Console\Command;

class ActualizarMaestroProductos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'actualizar:maestro-productos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualizar maestro de productos';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        dispatch(new ActualizarMaestroProductoWMS())->onQueue('prueba');

        $this->info('Trabajo de actualización de maestro de productos despachado.');
    }
}
