<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WMSStocksDisponibles;
use App\Models\cmbodega;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class WMSController extends Controller
{
    public function getStockDisponible(Request $request)
    {
        Log::info('Iniciando solicitud a la API externa.');

        // Obtener todos los códigos de bodega con bod_divisi = 'C'
        $bodegas = cmbodega::where('bod_divisi', 'C')->pluck('bod_codigo');

        foreach ($bodegas as $codDeposito) {
            // Realizar la solicitud GET a la API para cada código de bodega
            $response = Http::withHeaders([
                'dataAuth' => 'LWHXftRvCLqhW+IiQnHygDMOX2JHZv/KA387nvwqKijhrj3ehMg5VMXx+jT1GPRp'
            ])->get("http://198.1.1.122:1950/EnfasysWMS_Api/api/WMS_Inventory/GetStockDisponible", [
                'codOwner' => 'CALS',
                'codDeposito' => $codDeposito,
            ]);

            if ($response->successful()) {
                Log::info("Solicitud exitosa a la API externa para codDeposito $codDeposito.", ['response' => $response->json()]);

                $data = $response->json();
                foreach ($data['arrayStock'] as $item) {
                    WMSStocksDisponibles::create([
                        'bodega' => $item['codDeposito'],
                        'codigo' => $item['codItem'],
                        'cantidad' => $item['cantidad'],
                        'fecha' => Carbon::now()->format('Y-m-d')  // Asegúrate de que la fecha sea correcta
                    ]);
                }
            } else {
                $errorDetails = $response->body();
                Log::error("Error al obtener datos de la API para codDeposito $codDeposito.", ['status' => $response->status(), 'details' => $errorDetails]);
            }
        }

        return response()->json(['message' => 'Datos guardados correctamente'], 200);
    }
}
