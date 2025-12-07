<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductImage;
use App\Models\Inventory;

/**
 * Seeder pre testovacie produkty
 */
class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'Nike Air Max 270',
                'brand' => 'Nike',
                'sku_model' => 'NK-AM270-001',
                'base_price' => 149.99,
                'gender' => 'unisex',
                'description' => 'Ikonické tenisky Nike Air Max 270 s maximálnym pohodlím a štýlovým dizajnom. Vzduchová jednotka poskytuje celodenný komfort.',
                'image' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=600',
                'colors' => ['black', 'white', 'red'],
                'sizes' => ['40', '41', '42', '43', '44'],
            ],
            [
                'name' => 'Adidas Ultraboost 22',
                'brand' => 'Adidas',
                'sku_model' => 'AD-UB22-002',
                'base_price' => 179.99,
                'gender' => 'unisex',
                'description' => 'Prémiové bežecké tenisky s technológiou Boost pre maximálnu energiu pri každom kroku.',
                'image' => 'https://images.unsplash.com/photo-1608231387042-66d1773070a5?w=600',
                'colors' => ['black', 'white', 'blue'],
                'sizes' => ['39', '40', '41', '42', '43'],
            ],
            [
                'name' => 'Puma RS-X',
                'brand' => 'Puma',
                'sku_model' => 'PM-RSX-003',
                'base_price' => 99.99,
                'gender' => 'unisex',
                'description' => 'Retro futúristický dizajn s moderným komfortom. Ideálne pre každodenné nosenie.',
                'image' => 'https://images.unsplash.com/photo-1605733160314-4fc7dac4bb16?w=600',
                'colors' => ['white', 'pink', 'gray'],
                'sizes' => ['38', '39', '40', '41', '42'],
            ],
            [
                'name' => 'New Balance 574',
                'brand' => 'New Balance',
                'sku_model' => 'NB-574-004',
                'base_price' => 89.99,
                'gender' => 'unisex',
                'description' => 'Klasický model New Balance 574, ktorý nikdy nevyjde z módy. Kombinácia štýlu a pohodlia.',
                'image' => 'https://images.unsplash.com/photo-1539185441755-769473a23570?w=600',
                'colors' => ['gray', 'navy', 'green'],
                'sizes' => ['40', '41', '42', '43', '44', '45'],
            ],
            [
                'name' => 'Converse Chuck Taylor',
                'brand' => 'Converse',
                'sku_model' => 'CV-CT-005',
                'base_price' => 69.99,
                'gender' => 'unisex',
                'description' => 'Legendárne Converse Chuck Taylor - nadčasová klasika, ktorá patrí do každého šatníka.',
                'image' => 'https://images.unsplash.com/photo-1607522370275-f14206abe5d3?w=600',
                'colors' => ['black', 'white', 'red', 'navy'],
                'sizes' => ['36', '37', '38', '39', '40', '41', '42', '43'],
            ],
            [
                'name' => 'Vans Old Skool',
                'brand' => 'Vans',
                'sku_model' => 'VN-OS-006',
                'base_price' => 74.99,
                'gender' => 'unisex',
                'description' => 'Kultové skate tenisky Vans Old Skool s charakteristickým bočným pruhom.',
                'image' => 'https://images.unsplash.com/photo-1525966222134-fcfa99b8ae77?w=600',
                'colors' => ['black', 'white', 'checkerboard'],
                'sizes' => ['37', '38', '39', '40', '41', '42', '43', '44'],
            ],
        ];

        $productId = 1.0;
        $variantId = 1.0;

        foreach ($products as $productData) {
            // Vytvorenie produktu
            $product = Product::create([
                'product_id' => $productId,
                'name' => $productData['name'],
                'brand' => $productData['brand'],
                'sku_model' => $productData['sku_model'],
                'base_price' => $productData['base_price'],
                'gender' => $productData['gender'],
                'description' => $productData['description'],
                'is_active' => 1,
            ]);

            // Pridanie hlavného obrázka
            ProductImage::create([
                'product_id' => $productId,
                'image_path' => $productData['image'],
                'is_main' => 1,
                'sort_order' => 1,
            ]);

            // Vytvorenie variantov
            foreach ($productData['colors'] as $color) {
                foreach ($productData['sizes'] as $size) {
                    ProductVariant::create([
                        'variant_id' => $variantId,
                        'product_id' => $productId,
                        'sku' => strtoupper(substr($productData['brand'], 0, 2)) . "-{$productId}-" . strtoupper(substr($color, 0, 3)) . "-{$size}",
                        'color' => $color,
                        'size_eu' => $size,
                        'is_active' => 1,
                    ]);

                    // Náhodný sklad
                    Inventory::create([
                        'variant_id' => $variantId,
                        'stock_qty' => rand(0, 20),
                    ]);

                    $variantId += 1.0;
                }
            }

            $productId += 1.0;
        }

        $this->command->info('Vytvorených ' . count($products) . ' produktov s variantami.');
    }
}

