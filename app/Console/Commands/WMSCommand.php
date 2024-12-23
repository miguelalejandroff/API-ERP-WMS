<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class WMSCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:abstract {name : Nombre de la clase abstracta}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crea una nueva clase abstracta en app/WMS/Templates/Abstracts/';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $name = $this->argument('name');

        // Validar el nombre de la clase
        if (!$this->isValidClassName($name)) {
            $this->error("El nombre de la clase '{$name}' no es válido. Debe cumplir con las reglas de nomenclatura de PHP.");
            return Command::FAILURE;
        }

        // Definir ruta y nombre del archivo
        $path = app_path('WMS/Templates/Abstracts/');
        $filename = $path . $name . '.php';

        // Crear el directorio si no existe
        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
            $this->info("Directorio creado: {$path}");
        }

        // Verificar si el archivo ya existe
        if (File::exists($filename)) {
            $this->error("El archivo ya existe en: {$filename}");
            return Command::FAILURE;
        }

        // Definir el contenido de la clase
        $contents = <<<EOT
<?php

namespace App\WMS\Templates\Abstracts;

abstract class {$name} extends AbstractBase
{
    // Agrega tus métodos abstractos aquí
}
EOT;

        // Crear el archivo
        if (File::put($filename, $contents)) {
            $this->info("Clase abstracta creada exitosamente en: {$filename}");
            return Command::SUCCESS;
        } else {
            $this->error("No se pudo crear la clase abstracta en: {$filename}");
            return Command::FAILURE;
        }
    }

    /**
     * Verifica si el nombre de la clase es válido.
     *
     * @param string $name
     * @return bool
     */
    private function isValidClassName(string $name): bool
    {
        // Verifica que el nombre solo contenga caracteres válidos para clases en PHP
        return preg_match('/^[A-Z][A-Za-z0-9_]*$/', $name);
    }
}
