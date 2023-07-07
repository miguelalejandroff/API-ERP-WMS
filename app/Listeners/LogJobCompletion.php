<?php

namespace App\Listeners;

use App\Jobs\ProcesarProductosWMS;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use App\WMS\Jobs\ActualizarMaestroProductoWMS;

class LogJobCompletion
{
    protected $jobsToLog = [
        //ActualizarMaestroProductoWMS::class,
        //ProcesarProductosWMS::class
        // Añade aquí otras clases de trabajos para las que deseas registrar logs
    ];
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
    /**
     * Handle the event.
     *
     * @param  \Illuminate\Queue\Events\JobProcessed  $event
     * @return void
     */
    public function handle(JobProcessed $event)
    {
        if (in_array($event->job->resolveName(), $this->jobsToLog)) {
            $logData = [
                'job_id' => $event->job->getJobId(),
                'producto_id' => $event->job,
                'start_time' => now(),
                'end_time' => now(),
                'status' => 'completed'
            ];

            Log::info('Job completed', $logData);
        }
    }
}
