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
        $productGalleryPath = public_path('ProductGallery');  
        $productVarientPath = public_path('ProductVarient');

        // If your DB table and column for images is different, change this
        // Example: If images are in "products" table in "image" column:
        $productGalleryImages = DB::table('product_galleries')->pluck('image')->toArray(); 
        $productVarientImages = DB::table('product_varients')->pluck('image')->toArray(); 

        // Convert array to fast lookup
        $productGalleryImages = array_map('strtolower', $productGalleryImages);
        $productVarientImages = array_map('strtolower', $productVarientImages);

        // Get all files in folder
        $galleryFiles = File::files($productGalleryPath);
        $varientFiles = File::files($productVarientPath);

        $deletedCount = 0;
        foreach ($galleryFiles as $file) {
            $fileName = strtolower($file->getFilename());

            // If filename not in DB, delete it
            if (!in_array($fileName, $dbImages)) {
                File::delete($file->getPathname());
                $this->info("Deleted junk file: " . $fileName);
                $deletedCount++;
            }
        }

        foreach ($varientFiles as $file) {
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
