<?php

namespace App\Jobs;

use App\Models\cmproductos;
use Carbon\Carbon;
use Illuminate\Bus\Batch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;
use App\Http\Middleware\SetDatabaseConnection;
use App\Models\vpparsis;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;


class ActualizarMaestroProductoWMS implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public function __construct(
        protected $conn = 'informix'
    ) {
    }

    public function handle()
    {
        /*cmproductos::on($this->connection)->where('pro_anomes', '=', Carbon::now()->format('Ym'))
            ->chunk(200, function ($productos) {
                ProcesarProductosWMS::dispatch($productos);
            });*/
    }
}
