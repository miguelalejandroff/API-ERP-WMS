<?php



namespace App\ERP\Services;

use App\ERP\Contracts\InventarioService;
use Illuminate\Http\Request;
use App\Models\cminvent;
use App\Models\cmdetinv;
use Illuminate\Support\Facades\Log;

class InventarioServiceImpl implements InventarioService
{
    protected $context;

    public function __construct($context)
    {
        $this->context = $context;
    }

    public function run()
    {

        $data = $this->context->all();

        Log::info('Inicio de la operación de actualización en la base de datos.');

        // Log de información: Datos recibidos
        Log::info('Datos recibidos para la actualización:', $data);

        // Actualizar la tabla cminvent
        cminvent::updateOrCreate(
            ['inv_numgui' => $data['numeroDocumento']],
            [
                'inv_bodega' => $data['bodega'],
                'inv_fechai' => $data['fechaCierre'],
                'inv_codusu' => $data['usuario'],
                // Otros campos según la lógica proporcionada
            ]
        );

        Log::info('Operación de actualización en la tabla cminvent realizada con éxito.');

        // Obtener la colección de documentosDetalle
        $detalles = $data['documentoDetalle'];

        // Mapear la colección para preparar datos para la actualización en masa
        $updates = collect($detalles)->map(function ($detalle) use ($data) {
            return [
                'inv_numgui' => $data['numeroDocumento'],
                'inv_produc' => $detalle['codigoProducto'],
                'inv_descri' => $detalle['descripcion'],
                'inv_cantid' => $detalle['cantidad'],
                // Otros campos según la lógica proporcionada
            ];
        });

        Log::info('Datos para la actualización en masa:', $updates->toArray());

        // Actualizar la tabla cmdetinv en masa
        cmdetinv::upsert($updates, ['inv_numgui', 'inv_produc']);

        // Log de información: Operación completada
        Log::info('Operación de actualización en masa realizada con éxito.');

        // Log de información: Fin de la operación
        Log::info('Fin de la operación de actualización en la base de datos.');

    }
}
