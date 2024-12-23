<?php

namespace App\Http\Controllers;

use App\Models\mongodb\Tracking;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    /**
     * GET /tracking → index()
     * Recupera todos los registros de seguimiento.
     */
    public function index()
    {
        try {
            $trackings = Tracking::all();

            // Verificar errores en registros y filtrar resultados
            $trackingsWithIssues = $trackings->filter(function ($row) {
                return !is_null($row->errors) || $row->status != 200;
            });

            if ($trackingsWithIssues->isNotEmpty()) {
                return response()->json($trackingsWithIssues->values(), 200, [], JSON_PRETTY_PRINT);
            }

            // Si no hay problemas, devolver todos los registros
            return response()->json($trackings, 200, [], JSON_PRETTY_PRINT);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al recuperar los datos.', 'details' => $e->getMessage()], 500);
        }
    }

    /**
     * POST /tracking → store()
     * Almacena un nuevo registro.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'status' => 'required|numeric',
            'errors' => 'nullable|string',
            'data' => 'required|string',
        ]);

        try {
            $tracking = Tracking::create($validated);
            return response()->json(['message' => 'Registro creado correctamente', 'data' => $tracking], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al crear el registro.', 'details' => $e->getMessage()], 500);
        }
    }

    /**
     * GET /tracking/{id} → show()
     * Muestra un registro específico.
     */
    public function show($id)
    {
        $tracking = Tracking::find($id);

        if (!$tracking) {
            return response()->json(['error' => "Registro no encontrado: {$id}"], 404);
        }

        return response()->json($tracking, 200, [], JSON_PRETTY_PRINT);
    }

    /**
     * PUT /tracking/{id} → update()
     * Actualiza un registro específico.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'sometimes|numeric',
            'errors' => 'nullable|string',
            'data' => 'sometimes|string',
        ]);

        $tracking = Tracking::find($id);

        if (!$tracking) {
            return response()->json(['error' => "Registro no encontrado: {$id}"], 404);
        }

        try {
            $tracking->update($validated);
            return response()->json(['message' => 'Registro actualizado correctamente', 'data' => $tracking], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al actualizar el registro.', 'details' => $e->getMessage()], 500);
        }
    }

    /**
     * DELETE /tracking/{id} → destroy()
     * Elimina un registro específico.
     */
    public function destroy($id)
    {
        $tracking = Tracking::find($id);

        if (!$tracking) {
            return response()->json(['error' => "Registro no encontrado: {$id}"], 404);
        }

        try {
            $tracking->delete();
            return response()->json(['message' => 'Registro eliminado correctamente'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al eliminar el registro.', 'details' => $e->getMessage()], 500);
        }
    }
}
