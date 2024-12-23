<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\WMSController;
use Illuminate\Support\Facades\Http;

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
    protected $description = 'Envia la confirmación del stock disponible al WMS';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $controller = new WMSController();
        $controller->getStockDisponible(new \Illuminate\Http\Request());
    }
}
