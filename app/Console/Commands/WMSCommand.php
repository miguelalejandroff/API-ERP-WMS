<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class WMSCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:abstract {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new abstract class at app/WMS/Templates/Abstracts/';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $name = $this->argument('name');

        // Define the path and filename
        $path = app_path('WMS/Templates/Abstracts/');
        $filename = $path . $name . '.php';

        // Check if file already exists
        if (file_exists($filename)) {
            $this->error("File already exists at {$filename}");
            return 1;
        }

        // Define the contents of the class
        $contents = <<<EOT
            <?php

            namespace App\WMS\Templates\Abstracts;

            abstract class {$name} extends AbstractBase
            {
                        
            }
        EOT;

        // Create the file
        if (file_put_contents($filename, $contents) !== false) {
            $this->info("Abstract class created successfully at {$filename}");
        } else {
            $this->error("Failed to create abstract class at {$filename}");
        }


        return Command::SUCCESS;
    }
}
