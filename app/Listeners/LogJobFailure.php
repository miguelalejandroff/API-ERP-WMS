<?php

namespace App\Listeners;

use App\Jobs\ProcesarProductosWMS;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class LogJobFailure
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
     * @param  \Illuminate\Queue\Events\JobFailed  $event
     * @return void
     */
    public function handle(JobFailed $event)
    {
        if (in_array($event->job->resolveName(), $this->jobsToLog)) {

            $logData = [
                'job_id' => $event->job->getJobId(),
                'start_time' => now(),
                'end_time' => now(),
                'status' => 'failed'
            ];

            Log::error('Job failed', $logData);
        }
    }
}
