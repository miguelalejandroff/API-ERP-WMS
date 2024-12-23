<?php

namespace App\Providers;

use App\Listeners\LogJobCompletion;
use App\Listeners\LogJobFailure;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{

    // app/Providers/EventServiceProvider.php

        protected $listen = [
            'App\Events\ActualizarDesdeWMSEvent' => [
                'App\Listeners\ActualizarDesdeWMSListener',
            ],
];


    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
