<?php

namespace App\Jobs;

use App\Libs\WMS;
use App\Models\cmproductos;
use App\WMS\Templates\Implements\CreateItem;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcesarProductosWMS implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(protected $productos)
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->productos as $producto) {
            try {
                $createItem =  new CreateItem($producto);
                WMS::post('WMS_Admin/CreateItem', $createItem->getJson());
            } catch (Exception $e) {
                continue;
            }
        }
    }
}
