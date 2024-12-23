<?php

use Illuminate\Database\Capsule\Manager as Capsule;

// Carga el autoload de Composer (ajusta la ruta según tu estructura de archivos)
require __DIR__.'/vendor/autoload.php';

// Configura Eloquent para la conexión a MySQL
$capsule = new Capsule;

$capsule->setAsGlobal();
$capsule->bootEloquent();

// Configura Eloquent para la conexión a Informix
$capsule->addConnection([
    'driver'    => 'informix',
    'host'      => env('DB_IFX_HOST', 'localhost'),
    'database'  => env('DB_IFX_DATABASE', 'archi'),
    'username'  => env('DB_IFX_USERNAME', 'informix'),
    'password'  => env('DB_IFX_PASSWORD', 'informx15'),
    'service'   => env('DB_IFX_SERVICE', '1525'),
    'server'    => env('DB_IFX_SERVER', 'cals_shm'),
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

// Ahora deberías poder utilizar Eloquent en tu script

// Ejemplo de eliminación en Informix
$numeroDocumento = 1080728;
$BodegaDestino = 56;

// Utilizando consulta SQL para la eliminación en Informix
\App\Models\cmguinum::where('gui_numero', $numeroDocumento)
    ->where('gui_bodori', $BodegaDestino)
    ->delete();
