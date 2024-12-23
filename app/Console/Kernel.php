<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        /**
         * Tareas programadas para la aplicación
         */

        // Actualización de maestros
        $this->scheduleCommand(
            $schedule,
            'actualizar:maestro-grupos',
            '07:00',
            'Tarea actualizar:maestro-grupos'
        );

        $this->scheduleCommand(
            $schedule,
            'actualizar:maestro-rubros',
            '07:00',
            'Tarea actualizar:maestro-rubros'
        );

        // Confirmación de stock
        $this->scheduleCommand(
            $schedule,
            'stock:confirmar',
            'hourly',
            'Tarea stock:confirmar'
        );

        // Ejemplo de tarea personalizada (si es necesaria)
        // $schedule->job(new DeveloperJob)->everyMinute();
    }

    /**
     * Helper method to schedule commands with success and failure logs.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @param string $command
     * @param string $frequency
     * @param string $taskDescription
     * @return void
     */
    private function scheduleCommand(Schedule $schedule, string $command, string $frequency, string $taskDescription)
    {
        $task = $schedule->command($command);

        // Define la frecuencia de ejecución
        if ($frequency === 'hourly') {
            $task->hourly();
        } elseif ($frequency === 'daily') {
            $task->daily();
        } elseif (str_contains($frequency, ':')) {
            $task->dailyAt($frequency);
        }

        // Logs de éxito y error
        $task->onSuccess(function () use ($taskDescription) {
            Log::info("{$taskDescription} completada exitosamente.");
        })->onFailure(function () use ($taskDescription) {
            Log::error("Error al ejecutar {$taskDescription}.");
        });
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        // Cargar comandos del directorio Console/Commands
        $this->load(__DIR__ . '/Commands');

        // Registrar comandos definidos en routes/console.php
        require base_path('routes/console.php');
    }
}
