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
use Illuminate\Support\Facades\Request;

class ActualizarMaestroProductos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'actualizar:maestro-productos {limit=200}';

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

        $limit = $this->argument('limit');

        $count = 9000;

        //$this->info("Inicia Proceso de Carga Maestro Producto {$filename}");

        cmproductos::where('pro_anomes', Carbon::now()->format('Ym'))->orderBy('pro_codigo')
            ->chunk($limit, function ($items) use (&$count) {
                $array = [];

                $request = Request::instance();

                $tracking = Tracking::firstOrCreate(
                    ['document' => Carbon::now()->format('Ym'), 'type' => $count++],
                    ['tracking' => []]
                );

                $request->attributes->set('tracking', $tracking);

                foreach ($items as $itemData) {

                    $model = cmproductos::sku($itemData->pro_codigo);
                    $array[] = (new CreateItem($model))->get();
                    Log::info("producto: {$itemData->pro_codigo}");

                }

                WMS::post('WMS_Admin/CreateItem', response()->json([
                    'codOwner' => "CALS",
                    'item' => $array,
                ]));

                //Tracking::truncate();
            });
        /*
        dispatch(new ActualizarMaestroProductoWMS())->onQueue('prueba');

        $this->info('Trabajo de actualización de maestro de productos despachado.');*/
    }
}
