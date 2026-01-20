<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * ProductController - spracováva požiadavky pre produkty (frontend)
 *
 * Implementuje MVC pattern - Controller spája Model a View
 */
class ProductController extends Controller
{
    /**
     * Zobrazí zoznam produktov (hlavná stránka)
     * GET /
     */
    public function index(Request $request): View
    {
        $query = Product::active()
            ->with(['variants.inventory', 'images']);

        // Vyhľadávanie
        if ($request->filled('q')) {
            $query->search($request->input('q'));
        }

        // Filter podľa ceny
        if ($request->filled('min_price') || $request->filled('max_price')) {
            $query->priceRange(
                $request->input('min_price'),
                $request->input('max_price')
            );
        }

        // Filter podľa veľkostí
        if ($request->filled('sizes')) {
            $sizes = explode(',', $request->input('sizes'));
            $query->whereHas('variants', function ($q) use ($sizes) {
                $q->whereIn('size_eu', $sizes)->where('is_active', 1);
            });
        }

        // Filter podľa kategórie
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        // Filter podľa pohlavia (gender)
        if ($request->filled('gender')) {
            $query->where('gender', $request->input('gender'));
        }

        // Filter podľa farieb
        if ($request->filled('colors')) {
            $colors = explode(',', $request->input('colors'));
            $query->whereHas('variants', function ($q) use ($colors) {
                $q->whereIn('color', $colors)->where('is_active', 1);
            });
        }

        // Filter podľa značky
        if ($request->filled('brands')) {
            $brands = explode(',', $request->input('brands'));
            $query->whereIn('brand', $brands);
        }

        // Zoradenie
        $sort = $request->input('sort', 'default');
        switch ($sort) {
            case 'price-asc':
                $query->orderBy('base_price', 'asc');
                break;
            case 'price-desc':
                $query->orderBy('base_price', 'desc');
                break;
            case 'name-asc':
                $query->orderBy('name', 'asc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy('product_id', 'desc');
        }

        $products = $query->paginate(12);

        // Získame všetky veľkosti a farby pre filtre
        $allSizes = ProductVariant::where('is_active', 1)
            ->distinct()
            ->pluck('size_eu')
            ->filter()
            ->sort()
            ->values();

        $allColors = ProductVariant::where('is_active', 1)
            ->distinct()
            ->pluck('color')
            ->filter()
            ->sort()
            ->values();

        $allBrands = Product::active()
            ->distinct()
            ->pluck('brand')
            ->sort()
            ->values();

        return view('products.index', compact('products', 'allSizes', 'allColors', 'allBrands'));
    }

    /**
     * Zobrazí detail produktu
     * GET /product/{id}
     */
    public function show($id): View
    {
        $product = Product::with(['variants.inventory', 'images'])
            ->findOrFail($id);

        // Zoskupíme varianty podľa farby a veľkosti
        $variantsByColor = $product->variants
            ->where('is_active', 1)
            ->groupBy('color');

        return view('products.show', compact('product', 'variantsByColor'));
    }

    /**
     * API: Získa produkty vo formáte JSON
     * GET /api/products
     */
    public function apiIndex(Request $request)
    {
        $query = Product::active()
            ->with(['variants.inventory', 'images']);

        if ($request->filled('q')) {
            $query->search($request->input('q'));
        }

        if ($request->filled('min_price')) {
            $query->where('base_price', '>=', $request->input('min_price'));
        }

        if ($request->filled('max_price')) {
            $query->where('base_price', '<=', $request->input('max_price'));
        }

        $products = $query->orderBy('product_id', 'desc')->limit(200)->get();

        $data = $products->map(function ($product) {
            $sizes = $product->variants
                ->where('is_active', 1)
                ->pluck('size_eu')
                ->unique()
                ->values();

            $colors = $product->variants
                ->where('is_active', 1)
                ->pluck('color')
                ->unique()
                ->values();

            $mainImage = $product->main_image
                ?? "https://picsum.photos/seed/p{$product->product_id}/400/300";

            return [
                'id' => $product->product_id,
                'product_id' => $product->product_id,
                'name' => $product->name,
                'sku_model' => $product->sku_model,
                'brand' => $product->brand,
                'price' => (float) $product->base_price,
                'old_price' => null,
                'description' => $product->description,
                'image' => $mainImage,
                'variants' => $product->variants->map(function ($v) {
                    return [
                        'variant_id' => $v->variant_id,
                        'product_id' => $v->product_id,
                        'sku' => $v->sku,
                        'color' => $v->color,
                        'size_eu' => $v->size_eu,
                        'is_active' => $v->is_active,
                        'stock_qty' => $v->stock_qty,
                    ];
                }),
                'sizes' => $sizes,
                'colors' => $colors,
            ];
        });

        return response()->json(['ok' => true, 'data' => $data]);
    }

    /**
     * API: Získa detail produktu
     * GET /api/product/{id}
     */
    public function apiShow($id)
    {
        $product = Product::with(['variants.inventory', 'images'])->find($id);

        if (!$product) {
            return response()->json(['ok' => false, 'error' => 'Product not found'], 404);
        }

        $mainImage = $product->main_image
            ?? "https://picsum.photos/seed/p{$product->product_id}/400/300";

        $images = $product->images->map(function ($img) {
            return [
                'id' => $img->image_id,
                'path' => $img->image_path,
                'is_main' => (bool) $img->is_main,
            ];
        });

        return response()->json([
            'ok' => true,
            'data' => [
                'id' => $product->product_id,
                'name' => $product->name,
                'sku_model' => $product->sku_model,
                'brand' => $product->brand,
                'gender' => $product->gender,
                'price' => (float) $product->base_price,
                'description' => $product->description,
                'is_active' => $product->is_active,
                'image' => $mainImage,
                'images' => $images,
                'variants' => $product->variants->map(function ($v) {
                    return [
                        'variant_id' => $v->variant_id,
                        'sku' => $v->sku,
                        'color' => $v->color,
                        'size_eu' => $v->size_eu,
                        'is_active' => $v->is_active,
                        'stock_qty' => $v->stock_qty,
                    ];
                }),
                'sizes' => $product->available_sizes,
                'colors' => $product->available_colors,
            ]
        ]);
    }
}

