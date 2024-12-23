<?php

namespace App\Http\Controllers;

use App\Models\cmproductos;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Cache;

class MaestroProductoController extends Controller
{
    /**
     * Genera un archivo CSV con productos y sus códigos de barra.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Recuperar los productos con la relación 'wmscodigobarra'
        $productos = cmproductos::with('wmscodigobarra')->paginate(10); // Paginación de 10 productos

        // Retornar la vista con los productos
        return view('productos.index', compact('productos'));
    }

    public function descargarCSV()
    {
        Log::info('Inicio de generación del Kardex CSV con clasificación ABC.');

        // Configurar nombre del archivo
        $nombreArchivo = 'kardex_abc_' . date('Y-m-d') . '.csv';

        // Configurar encabezados del archivo CSV
        $encabezados = [
            'Código Producto',
            'Descripción Producto',
            'Stock Inicial',
            'Entradas Totales',
            'Salidas Totales',
            'Stock Final',
            'Valor Total',
            'Categoría ABC'
        ];

        return response()->stream(
            function () use ($encabezados) {
                $handle = fopen('php://output', 'w');
                fputcsv($handle, $encabezados);

                // Obtener los productos
                $productos = cmproductos::all();
                $productosResumen = [];

                // Calcular los movimientos de cada producto
                foreach ($productos as $producto) {
                    $movimientos = \DB::table('cmdetgui as d')
                        ->join('cmguias as g', 'd.gui_numero', '=', 'g.gui_numero')
                        ->where('d.gui_produc', $producto->pro_codigo)
                        ->select(
                            'd.gui_canrep as cantidad',
                            'd.gui_preuni as costo_unitario',
                            'd.gui_tipgui as tipo_movimiento'
                        )->get();

                    $entradas = $salidas = $valorTotal = 0;

                    foreach ($movimientos as $movimiento) {
                        $cantidad = $movimiento->cantidad;
                        $costo = $movimiento->costo_unitario;

                        if ($movimiento->tipo_movimiento == '02') {
                            $entradas += $cantidad;
                            $valorTotal += $cantidad * $costo; // Calcular valor de entradas
                        } elseif ($movimiento->tipo_movimiento == '10') {
                            $salidas += $cantidad;
                        }
                    }

                    $stockInicial = $producto->pro_stockp;
                    $stockFinal = $stockInicial + $entradas - $salidas;

                    $productosResumen[] = [
                        'codigo' => $producto->pro_codigo,
                        'descripcion' => $producto->pro_descri,
                        'stock_inicial' => $stockInicial,
                        'entradas' => $entradas,
                        'salidas' => $salidas,
                        'stock_final' => $stockFinal,
                        'valor_total' => $valorTotal,
                    ];
                }

                // Ordenar productos por valor total en orden descendente
                usort($productosResumen, function ($a, $b) {
                    return $b['valor_total'] <=> $a['valor_total'];
                });

                // Calcular el porcentaje acumulado y asignar categoría ABC
                $valorTotalGeneral = array_sum(array_column($productosResumen, 'valor_total'));
                $acumulado = 0;

                foreach ($productosResumen as &$producto) {
                    $acumulado += $producto['valor_total'];
                    $porcentajeAcumulado = ($acumulado / $valorTotalGeneral) * 100;

                    if ($porcentajeAcumulado <= 80) {
                        $producto['categoria'] = 'A';
                    } elseif ($porcentajeAcumulado <= 95) {
                        $producto['categoria'] = 'B';
                    } else {
                        $producto['categoria'] = 'C';
                    }

                    // Escribir cada fila en el CSV
                    fputcsv($handle, [
                        $producto['codigo'],
                        $producto['descripcion'],
                        $producto['stock_inicial'],
                        $producto['entradas'],
                        $producto['salidas'],
                        $producto['stock_final'],
                        number_format($producto['valor_total'], 2),
                        $producto['categoria']
                    ]);
                }

                fclose($handle);
            },
            200,
            [
                'Content-Type' => 'text/csv; charset=utf-8',
                'Content-Disposition' => 'attachment; filename="kardex_abc_' . date('Y-m-d') . '.csv"',
            ]
        );
    }


    /**
     * Detecta si algún valor tiene caracteres especiales.
     *
     * @param array $campos
     * @return array
     */
    private function detectarCaracteresEspeciales(array $campos): array
    {
        $camposConEspeciales = [];
        foreach ($campos as $nombreCampo => $valor) {
            if (preg_match('/[^a-zA-Z0-9\s]/', $valor)) {
                $camposConEspeciales[] = $nombreCampo;
            }
        }
        return $camposConEspeciales;
    }
}
