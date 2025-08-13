<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class CleanImageFolder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Example usage: php artisan clean:images
     */
    protected $signature = 'clean:images';

    /**
     * The console command description.
     */
    protected $description = 'Delete junk images that are not referenced in the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Adjust this to your image folder path
        $folderPath = public_path('ProductGallery');  

        // If your DB table and column for images is different, change this
        // Example: If images are in "products" table in "image" column:
        $dbImages = DB::table('product_galleries')->pluck('image')->toArray(); 

        // Convert array to fast lookup
        $dbImages = array_map('strtolower', $dbImages);

        // Get all files in folder
        $files = File::files($folderPath);

        $deletedCount = 0;
        foreach ($files as $file) {
            $fileName = strtolower($file->getFilename());

            // If filename not in DB, delete it
            if (!in_array($fileName, $dbImages)) {
                File::delete($file->getPathname());
                $this->info("Deleted junk file: " . $fileName);
                $deletedCount++;
            }
        }

        $this->info("Clean up complete. Deleted {$deletedCount} junk files.");
        return Command::SUCCESS;
    }
}
