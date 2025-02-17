<?php
/* 
namespace App\Http\Controllers;

use App\DTE\PdfDtes;
use App\Libs\WMS;
use App\Models\cmdetord;
use App\Models\cmguias;
use App\Models\cmguinum;
use App\Models\cmordcom;
use App\Models\cmproductos;
use App\Models\cmsalbod;
use App\Models\guicompra;
use App\Models\mongodb\Saldos;
use App\Models\pedidosdetalles;
use App\Models\prueba;
use App\Models\vpparsis;
use App\WMS\Adapters\CreateItem;
use App\WMS\Adapters\OrdenEntrada\GuiaCompra;
use App\WMS\Adapters\OrdenEntrada\OrdenCompraRecepcion;
use App\WMS\Templates\Implements\CreateItem as ImplementsCreateItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Smalot\PdfParser\Parser;

class developer extends Controller
{
    public $table = 'sgv_ventas';

    public function dev(Request $request)
    {
        // Ejemplo de eliminación en Informix
        $numeroDocumento = 1080728;
        $BodegaDestino = 56;

        // Utilizando consulta SQL para la eliminación en Informix
        $guia = cmguinum::where('gui_numero', $numeroDocumento)
            ->where('gui_bodori', $BodegaDestino)
            ->first();
        //$guia = $guia->delete();
        dd($guia);
        dd(pedidosdetalles::first()->correlativounico);

        /--$producto = new ImplementsCreateItem(cmproductos::Sku('0653403'));
        $newData = $producto->getJson();
        dd(json_encode($newData));--/
        //return prueba::get();

        //dd(DB::connection('informix')->select('select * from vpparsis'));
        $ordenBase = new GuiaCompra(guicompra::Orden(2310884));
        $newData = $ordenBase->getJson();
        dd($newData);
        dd(PdfDtes::send('81643200-6', 52, 974679, '2023-06-06', 940000, 0));
        dd([Carbon::now()->addYear(), WMS::nowYear()]);
        /--
        $directory = 'E://LRV'; // directorio a buscar
        $files = scandir($directory);
        $files = array_diff($files, array('..', '.'));
        --/
        $prueba = new Saldos();

        //$prueba->save();
        dd($prueba->get());

        //dd((new GuiaRecepcion($request))->correlativo());
        dd(cmproductos::where('pro_anomes', '202303')->where('pro_codigo', '0011401')->first());
        $trackingId = uniqid();
        Log::build([
            'driver' => 'single', //'path' => "logs/{$trackingId}.log"
            'path' => storage_path("logs/{$trackingId}.log"),
        ])->error('Something happened!', ['trackingId' => $trackingId]);
        dd(cmdetord::where('ord_numcom', 2304953)->first()->calculaCosto);
        dd("hola");
        dd(hash('sha256', time()), time(), date('d.m.Y H:i:s',  time()));
        dd(cmsalbod::where('bod_ano', 2023)->where('bod_produc', '0011053')->where('bod_bodega', 1)->first());
        //$model = $cmsalbod->where('bod_bodega', 84)->where('bod_produc', "0011053")->where('bod_ano', 2023);
        //$model->bod_stockb = 20;
        //$model->decrement('bod_stockb', 13);
        //$model->save();
        /*$model->update([
            //$this->periodo() => DB::raw("{$this->periodo()} + {20}"),
            'bod_stockb' => DB::raw("bod_stockb + {20}")
        ]);--/
        //(new SaldoBodega)->quitar(84, '0011053', 14);
        //SaldoBodega::agregar(84, '0011053', 13);
        //return (new SaldoBodega)->agregar(84, '0011053', 14);
        dd(cmsalbod::where('bod_ano', 2023)->where('bod_produc', '0011053')->where('bod_bodega', 84)->first());
    }
}*/
namespace App\Http\Controllers;

use App\Models\cmguinum;
use App\Models\cmsalbod;
use App\Models\cmproductos;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DeveloperController extends Controller
{
    public $table = 'sgv_ventas';

    public function dev(Request $request)
    {
        try {
            // Prueba de obtención de guía
            $numeroDocumento = 1080728;
            $BodegaDestino = 56;
            $guia = cmguinum::where('gui_numero', $numeroDocumento)
                ->where('gui_bodori', $BodegaDestino)
                ->first();

            if (!$guia) {
                Log::info("No se encontró la guía: {$numeroDocumento}");
                return response()->json(['error' => 'Guía no encontrada'], 404);
            }

            // Ejemplo de uso de Carbon
            $fechaActual = Carbon::now();
            $fechaFutura = $fechaActual->addYear();
            Log::info("Fecha actual: {$fechaActual}, Fecha futura: {$fechaFutura}");

            // Consulta de productos
            $producto = cmproductos::where('pro_anomes', '202303')
                ->where('pro_codigo', '0011401')
                ->first();

            if ($producto) {
                Log::info("Producto encontrado: {$producto->pro_codigo}");
            } else {
                Log::warning("Producto no encontrado");
            }

            // Respuesta final para pruebas
            return response()->json([
                'guia' => $guia,
                'producto' => $producto,
                'fechaActual' => $fechaActual,
                'fechaFutura' => $fechaFutura,
            ]);

        } catch (\Exception $e) {
            Log::error("Error en DeveloperController: {$e->getMessage()}");
            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }
}
