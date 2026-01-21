<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductImage;
use App\Models\Inventory;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Prázdny seeder - produkty sa pridávajú manuálne cez admin panel
        $this->command->info('ProductSeeder je prázdny. Produkty pridajte cez admin panel.');
    }
}
