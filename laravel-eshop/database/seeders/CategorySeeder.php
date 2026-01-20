<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Tenisky',
                'slug' => 'tenisky',
                'description' => 'Všetky druhy tenisiek pre šport a voľný čas.',
                'sort_order' => 1,
            ],
            [
                'name' => 'Doplnky',
                'slug' => 'doplnky',
                'description' => 'Ponožky, čiapky a iné doplnky.',
                'sort_order' => 2,
            ],
            [
                'name' => 'Beh',
                'slug' => 'beh',
                'description' => 'Špeciálna obuv na behanie.',
                'sort_order' => 3,
            ],
            [
                'name' => 'Lifestyle',
                'slug' => 'lifestyle',
                'description' => 'Pohodlná obuv na každý deň.',
                'sort_order' => 4,
            ],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(
                ['slug' => $cat['slug']],
                $cat
            );
        }

        $this->command->info('Kategórie boli vytvorené.');
    }
}
