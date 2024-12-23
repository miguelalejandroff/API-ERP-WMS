<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WMSStocksDisponibles;
use App\Models\cmbodega;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;

class WMSController extends Controller
{
    public function getStockDisponible(Request $request)
    {
        Log::info('Iniciando solicitud a la API externa para obtener stock disponible.');

        try {
            // Obtener todos los códigos de bodega con bod_divisi = 'C'
            $bodegas = cmbodega::where('bod_divisi', 'C')->pluck('bod_codigo');

            $headers = [
                'dataAuth' => 'LWHXftRvCLqhW+IiQnHygDMOX2JHZv/KA387nvwqKijhrj3ehMg5VMXx+jT1GPRp'
            ];

            $now = Carbon::now()->format('Y-m-d');

            foreach ($bodegas as $codDeposito) {
                try {
                    // Realizar la solicitud GET a la API
                    $response = Http::withHeaders($headers)->timeout(10)->retry(3, 1000)->get(
                        "http://198.1.1.122:1950/EnfasysWMS_Api/api/WMS_Inventory/GetStockDisponible",
                        [
                            'codOwner' => 'CALS',
                            'codDeposito' => $codDeposito,
                        ]
                    );

                    if ($response->successful()) {
                        $data = $response->json();
                        Log::info("Solicitud exitosa a la API externa para codDeposito $codDeposito.");

                        // Validar y preparar los datos para la inserción
                        $stocks = [];
                        foreach ($data['arrayStock'] as $item) {
                            $stocks[] = [
                                'bodega' => $item['codDeposito'],
                                'codigo' => $item['codItem'],
                                'cantidad' => $item['cantidad'],
                                'fecha' => $now,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }

                        // Insertar datos evitando duplicados (bodega y código como claves únicas)
                        WMSStocksDisponibles::upsert(
                            $stocks,
                            ['bodega', 'codigo'], // Claves únicas
                            ['cantidad', 'fecha', 'updated_at'] // Campos a actualizar
                        );

                        Log::info("Datos guardados correctamente para codDeposito $codDeposito.");
                    } else {
                        Log::error("Error en la API para codDeposito $codDeposito.", [
                            'status' => $response->status(),
                            'error' => $response->body()
                        ]);
                    }
                } catch (Exception $e) {
                    Log::error("Error durante la solicitud para codDeposito $codDeposito: " . $e->getMessage());
                }
            }

            return response()->json(['message' => 'Proceso completado correctamente'], 200);
        } catch (Exception $e) {
            Log::critical('Error crítico en getStockDisponible: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener datos del stock', 'details' => $e->getMessage()], 500);
        }
    }
}
