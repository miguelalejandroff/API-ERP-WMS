<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Maintenance Mode Check
|--------------------------------------------------------------------------
|
| Quickly return the maintenance mode response, if the application is down.
| This avoids bootstrapping the full application stack unnecessarily.
|
*/
if (file_exists($maintenance = __DIR__ . '/../storage/framework/maintenance.php')) {
    require $maintenance;
    exit; // Termina la ejecución para mayor seguridad.
}

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient class loader for automatically loading
| dependencies. This eliminates the need for manual loading of classes.
|
*/
require_once __DIR__ . '/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Here, we bootstrap the application, handle the request, and return the
| response. Finally, we clean up and terminate the kernel.
|
*/
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Kernel::class);

// Aumentar el límite de memoria solo si es necesario
if (ini_get('memory_limit') < '512M') {
    ini_set('memory_limit', '512M');
}

$response = $kernel->handle(
    $request = Request::capture()
);

$response->send();

$kernel->terminate($request, $response);
