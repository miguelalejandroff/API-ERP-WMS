<?php

// app/Listeners/ActualizarDesdeWMSListener.php

namespace App\Listeners;

use App\Events\ActualizarDesdeWMSEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\pedidosdetalles;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ActualizarDesdeWMSListener implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(ActualizarDesdeWMSEvent $event)
    {
        try {
            DB::beginTransaction();

            pedidosdetalles::where([
                'ped_folio' => $event->pedidosQuery,
                'ped_codrub' => $event->ultimoDigito
            ])->update([
            'ped_estped' => 'M',
            'ped_nomestado' => 'EN MATRIZ',
                // Otros campos que necesitas actualizar en despachodetalle
            ]);
            
            DB::commit();
            Log::info('Pedido actualizado desde WMS correctamente.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error al actualizar desde WMS:', ['error_message' => $e->getMessage()]);
        }
    }
}

