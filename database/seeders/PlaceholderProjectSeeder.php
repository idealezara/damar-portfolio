<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Project;
use Illuminate\Database\Seeder;

/**
 * OPTIONAL: php artisan db:seed --class=PlaceholderProjectSeeder
 * Adds clearly labelled placeholders so the gallery is not empty during setup.
 * Delete or edit them in Admin > Projects and upload real thumbnails.
 */
class PlaceholderProjectSeeder extends Seeder
{
    public function run(): void
    {
        $category = Category::firstOrCreate(['slug' => 'web-app'], ['name' => 'Web App']);

        foreach (['One', 'Two', 'Three'] as $n) {
            $title = "Placeholder Project {$n}";

            Project::firstOrCreate(['title' => $title], [
                'category_id' => $category->id,
                'slug' => Project::uniqueSlug($title),
                'short_description' => 'Placeholder - replace this from Admin > Projects.',
                'description' => "This is a placeholder entry.\n\nEdit or delete it in the admin dashboard and upload a real thumbnail.",
                'is_published' => true,
            ]);
        }
    }
}
