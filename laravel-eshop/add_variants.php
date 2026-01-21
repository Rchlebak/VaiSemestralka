<?php

// Spustite tento skript cez: docker compose exec web php add_variants.php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Inventory;

$sizes = ['40', '41', '42', '43', '44'];
$products = Product::with('variants')->get();

$addedVariants = 0;

// Získaj maximálne variant_id
$maxVariantId = ProductVariant::max('variant_id') ?? 0;

foreach ($products as $product) {
    // Získaj existujúcu farbu z prvého variantu alebo použi 'default'
    $existingVariant = $product->variants->first();
    $color = $existingVariant ? $existingVariant->color : 'default';

    // Získaj existujúce veľkosti pre tento produkt
    $existingSizes = $product->variants->pluck('size_eu')->toArray();

    // Pridaj chýbajúce veľkosti
    foreach ($sizes as $size) {
        if (!in_array($size, $existingSizes)) {
            $maxVariantId++;

            // Vytvor nový variant
            ProductVariant::create([
                'variant_id' => $maxVariantId,
                'product_id' => $product->product_id,
                'sku' => strtoupper(substr($product->brand ?? 'XX', 0, 2)) . "-{$product->product_id}-" . strtoupper(substr($color, 0, 3)) . "-{$size}",
                'color' => $color,
                'size_eu' => $size,
                'is_active' => 1,
            ]);

            // Pridaj inventár s náhodným skladom (variant_id je primary key)
            Inventory::create([
                'variant_id' => $maxVariantId,
                'stock_qty' => rand(5, 25)
            ]);

            $addedVariants++;
        }
    }

    echo "Produkt '{$product->name}' - farba: {$color}\n";
}

echo "\n=== HOTOVO ===\n";
echo "Pridaných variantov: {$addedVariants}\n";
