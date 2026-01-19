<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductImage;
use App\Models\Inventory;
use App\Http\Requests\ProductRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * AdminProductController - CRUD operácie pre produkty v admin rozhraní
 *
 * Implementuje kompletné CRUD (Create, Read, Update, Delete)
 */
class AdminProductController extends Controller
{
    /**
     * Zobrazí zoznam produktov
     * GET /admin/products
     */
    public function index(): View
    {
        $products = Product::with(['variants.inventory', 'images'])
            ->orderBy('product_id', 'desc')
            ->paginate(20);

        return view('admin.products.index', compact('products'));
    }

    /**
     * Zobrazí formulár pre vytvorenie produktu
     * GET /admin/products/create
     */
    public function create(): View
    {
        return view('admin.products.create');
    }

    /**
     * Uloží nový produkt
     * POST /admin/products
     */
    public function store(ProductRequest $request): RedirectResponse
    {
        DB::beginTransaction();

        try {
            $product = new Product();
            $product->product_id = Product::generateNewId();
            $product->fill($request->validated());
            $product->is_active = $request->has('is_active') ? 1 : 0;
            $product->save();

            // Spracovanie obrázkov
            $this->handleImages($request, $product);

            DB::commit();

            return redirect()
                ->route('admin.products.edit', $product->product_id)
                ->with('success', 'Produkt bol úspešne vytvorený');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Chyba pri vytváraní produktu: ' . $e->getMessage());
        }
    }

    /**
     * Zobrazí formulár pre úpravu produktu
     * GET /admin/products/{id}/edit
     */
    public function edit($id): View
    {
        $product = Product::with(['variants.inventory', 'images'])
            ->findOrFail($id);

        return view('admin.products.edit', compact('product'));
    }

    /**
     * Aktualizuje produkt
     * PUT /admin/products/{id}
     */
    public function update(ProductRequest $request, $id): RedirectResponse
    {
        DB::beginTransaction();

        try {
            $product = Product::findOrFail($id);
            $product->fill($request->validated());
            $product->is_active = $request->has('is_active') ? 1 : 0;
            $product->save();

            // Spracovanie obrázkov
            $this->handleImages($request, $product);

            DB::commit();

            return redirect()
                ->route('admin.products.edit', $product->product_id)
                ->with('success', 'Produkt bol úspešne aktualizovaný');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Chyba pri aktualizácii produktu: ' . $e->getMessage());
        }
    }

    /**
     * Vymaže produkt
     * DELETE /admin/products/{id}
     */
    public function destroy($id): RedirectResponse
    {
        DB::beginTransaction();

        try {
            $product = Product::findOrFail($id);

            // Vymazanie súvisiacich dát
            foreach ($product->variants as $variant) {
                Inventory::where('variant_id', $variant->variant_id)->delete();
            }
            ProductVariant::where('product_id', $id)->delete();
            ProductImage::where('product_id', $id)->delete();
            $product->delete();

            DB::commit();

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'Produkt bol úspešne vymazaný');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Chyba pri mazaní produktu: ' . $e->getMessage());
        }
    }

    /**
     * Pridá variant produktu
     * POST /admin/products/{id}/variants
     */
    public function addVariant(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'color' => 'required|string|max:50',
            'size_eu' => 'required|string|max:4',
            'stock_qty' => 'required|integer|min:0',
        ], [
            'color.required' => 'Farba je povinná',
            'size_eu.required' => 'Veľkosť je povinná',
            'stock_qty.required' => 'Množstvo na sklade je povinné',
        ]);

        DB::beginTransaction();

        try {
            $product = Product::findOrFail($id);

            $variant = new ProductVariant();
            $variant->variant_id = ProductVariant::generateNewId();
            $variant->product_id = $id;
            $variant->color = $request->input('color');
            $variant->size_eu = $request->input('size_eu');
            $variant->sku = ProductVariant::generateSku(
                $product,
                $request->input('color'),
                $request->input('size_eu')
            );
            $variant->is_active = 1;
            $variant->save();

            // Vytvorenie záznamu v inventári
            $inventory = new Inventory();
            $inventory->variant_id = $variant->variant_id;
            $inventory->stock_qty = $request->input('stock_qty', 0);
            $inventory->save();

            DB::commit();

            return back()->with('success', 'Variant bol úspešne pridaný');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Chyba pri pridávaní variantu: ' . $e->getMessage());
        }
    }

    /**
     * Aktualizuje sklad variantu
     * PUT /admin/variants/{variantId}/stock
     */
    public function updateStock(Request $request, $variantId): RedirectResponse
    {
        $request->validate([
            'stock_qty' => 'required|integer|min:0',
        ]);

        $inventory = Inventory::find($variantId);

        if (!$inventory) {
            $inventory = new Inventory();
            $inventory->variant_id = $variantId;
        }

        $inventory->stock_qty = $request->input('stock_qty');
        $inventory->save();

        return back()->with('success', 'Sklad bol aktualizovaný');
    }

    /**
     * API: Aktualizuje sklad variantu (AJAX in-place editing)
     * PUT /api/admin/variants/{variantId}/stock
     * 
     * Zmysluplné AJAX volanie č.2 - in-place editing
     */
    public function apiUpdateStock(Request $request, $variantId)
    {
        $validated = $request->validate([
            'stock_qty' => 'required|integer|min:0|max:99999',
        ], [
            'stock_qty.required' => 'Množstvo je povinné',
            'stock_qty.integer' => 'Množstvo musí byť celé číslo',
            'stock_qty.min' => 'Množstvo nemôže byť záporné',
        ]);

        try {
            $inventory = Inventory::find($variantId);

            if (!$inventory) {
                $inventory = new Inventory();
                $inventory->variant_id = $variantId;
            }

            $inventory->stock_qty = $validated['stock_qty'];
            $inventory->save();

            $variant = ProductVariant::find($variantId);

            return response()->json([
                'ok' => true,
                'message' => 'Sklad bol aktualizovaný',
                'data' => [
                    'variant_id' => $variantId,
                    'stock_qty' => $inventory->stock_qty,
                    'variant_info' => $variant ? "{$variant->color} / {$variant->size_eu}" : null
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'ok' => false,
                'error' => 'Chyba pri aktualizácii skladu',
                'detail' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Vymaže variant
     * DELETE /admin/variants/{variantId}
     */
    public function deleteVariant($variantId): RedirectResponse
    {
        DB::beginTransaction();

        try {
            Inventory::where('variant_id', $variantId)->delete();
            ProductVariant::where('variant_id', $variantId)->delete();

            DB::commit();

            return back()->with('success', 'Variant bol vymazaný');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Chyba pri mazaní variantu');
        }
    }

    /**
     * Vymaže obrázok
     * DELETE /admin/images/{imageId}
     */
    public function deleteImage($imageId): RedirectResponse
    {
        $image = ProductImage::findOrFail($imageId);

        // Vymazanie súboru ak existuje
        if (strpos($image->image_path, 'storage/') === 0) {
            $path = str_replace('storage/', '', $image->image_path);
            Storage::disk('public')->delete($path);
        }

        $image->delete();

        return back()->with('success', 'Obrázok bol vymazaný');
    }

    /**
     * Nastaví hlavný obrázok
     * POST /admin/images/{imageId}/main
     */
    public function setMainImage($imageId): RedirectResponse
    {
        $image = ProductImage::findOrFail($imageId);

        // Zrušenie hlavného obrázka pre ostatné
        ProductImage::where('product_id', $image->product_id)
            ->update(['is_main' => 0]);

        // Nastavenie tohto ako hlavného
        $image->is_main = 1;
        $image->save();

        return back()->with('success', 'Hlavný obrázok bol nastavený');
    }

    /**
     * Spracovanie obrázkov produktu
     */
    private function handleImages(Request $request, Product $product): void
    {
        // Povolené typy a maximálna veľkosť
        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 5 * 1024 * 1024; // 5MB

        // Pridanie obrázkov z URL
        if ($request->filled('image_urls')) {
            $urls = array_filter(explode("\n", $request->input('image_urls')));
            $maxSort = ProductImage::where('product_id', $product->product_id)->max('sort_order') ?? 0;

            foreach ($urls as $url) {
                $url = trim($url);
                if (filter_var($url, FILTER_VALIDATE_URL)) {
                    $existingCount = ProductImage::where('product_id', $product->product_id)->count();

                    ProductImage::create([
                        'product_id' => $product->product_id,
                        'image_path' => $url,
                        'is_main' => $existingCount === 0 ? 1 : 0,
                        'sort_order' => ++$maxSort,
                    ]);
                }
            }
        }

        // Nahrávanie súborov s validáciou
        if ($request->hasFile('images')) {
            $maxSort = ProductImage::where('product_id', $product->product_id)->max('sort_order') ?? 0;

            foreach ($request->file('images') as $file) {
                // Validácia typu súboru
                if (!in_array($file->getMimeType(), $allowedMimes)) {
                    continue; // Preskočiť nepodporované typy
                }

                // Validácia veľkosti
                if ($file->getSize() > $maxSize) {
                    continue; // Preskočiť príliš veľké súbory
                }

                if ($file->isValid()) {
                    $path = $file->store('products', 'public');
                    $existingCount = ProductImage::where('product_id', $product->product_id)->count();

                    ProductImage::create([
                        'product_id' => $product->product_id,
                        'image_path' => 'storage/' . $path,
                        'is_main' => $existingCount === 0 ? 1 : 0,
                        'sort_order' => ++$maxSort,
                    ]);
                }
            }
        }
    }

    /**
     * API: Vymaže obrázok (AJAX)
     * DELETE /api/admin/images/{imageId}
     */
    public function apiDeleteImage($imageId)
    {
        try {
            $image = ProductImage::findOrFail($imageId);

            // Vymazanie súboru ak existuje
            if (strpos($image->image_path, 'storage/') === 0) {
                $path = str_replace('storage/', '', $image->image_path);
                Storage::disk('public')->delete($path);
            }

            $image->delete();

            return response()->json([
                'ok' => true,
                'message' => 'Obrázok bol vymazaný'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'ok' => false,
                'error' => 'Chyba pri mazaní obrázka'
            ], 500);
        }
    }

    /**
     * API: Nastaví hlavný obrázok (AJAX)
     * POST /api/admin/images/{imageId}/main
     */
    public function apiSetMainImage($imageId)
    {
        try {
            $image = ProductImage::findOrFail($imageId);

            // Zrušenie hlavného obrázka pre ostatné
            ProductImage::where('product_id', $image->product_id)
                ->update(['is_main' => 0]);

            // Nastavenie tohto ako hlavného
            $image->is_main = 1;
            $image->save();

            return response()->json([
                'ok' => true,
                'message' => 'Hlavný obrázok bol nastavený'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'ok' => false,
                'error' => 'Chyba pri nastavovaní hlavného obrázka'
            ], 500);
        }
    }

    /**
     * API: Získa zoznam produktov
     * GET /api/admin/products
     */
    public function apiIndex()
    {
        $products = Product::with(['variants.inventory', 'images'])
            ->orderBy('product_id', 'desc')
            ->limit(1000)
            ->get();

        $data = $products->map(function ($product) {
            return [
                'id' => $product->product_id,
                'name' => $product->name,
                'sku_model' => $product->sku_model,
                'brand' => $product->brand,
                'price' => (float) $product->base_price,
                'is_active' => $product->is_active,
                'main_image' => $product->main_image,
                'variants_count' => $product->variants->count(),
            ];
        });

        return response()->json(['ok' => true, 'data' => $data]);
    }

    /**
     * API: Vytvorí produkt
     * POST /api/admin/products
     */
    public function apiStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|min:2|max:200',
            'price' => 'required|numeric|min:0.01|max:99999.99',
            'brand' => 'nullable|string|max:200',
            'sku_model' => 'nullable|string|max:84',
            'gender' => 'nullable|in:men,women,unisex',
            'description' => 'nullable|string',
            'image_url' => 'nullable|url',
        ], [
            'name.required' => 'Názov je povinný',
            'name.min' => 'Názov musí mať aspoň 2 znaky',
            'price.required' => 'Cena je povinná',
            'price.min' => 'Cena musí byť kladné číslo',
        ]);

        DB::beginTransaction();

        try {
            $product = new Product();
            $product->product_id = Product::generateNewId();
            $product->name = $validated['name'];
            $product->base_price = $validated['price'];
            $product->brand = $validated['brand'] ?? null;
            $product->sku_model = $validated['sku_model'] ?? null;
            $product->gender = $validated['gender'] ?? 'unisex';
            $product->description = $validated['description'] ?? null;
            $product->is_active = $request->input('is_active', 1) ? 1 : 0;
            $product->save();

            // Pridanie obrázka z URL
            if (!empty($validated['image_url'])) {
                ProductImage::create([
                    'product_id' => $product->product_id,
                    'image_path' => $validated['image_url'],
                    'is_main' => 1,
                    'sort_order' => 1,
                ]);
            }

            DB::commit();

            return response()->json(['ok' => true, 'id' => $product->product_id]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'ok' => false,
                'error' => 'Insert failed',
                'detail' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Aktualizuje produkt
     * PUT /api/admin/products/{id}
     */
    public function apiUpdate(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['ok' => false, 'error' => 'Product not found'], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|min:2|max:200',
            'price' => 'sometimes|numeric|min:0.01|max:99999.99',
            'brand' => 'nullable|string|max:200',
            'sku_model' => 'nullable|string|max:84',
            'gender' => 'nullable|in:men,women,unisex',
            'description' => 'nullable|string',
            'image_url' => 'nullable|url',
        ]);

        DB::beginTransaction();

        try {
            if (isset($validated['name'])) {
                $product->name = $validated['name'];
            }
            if (isset($validated['price'])) {
                $product->base_price = $validated['price'];
            }
            if (array_key_exists('brand', $validated)) {
                $product->brand = $validated['brand'];
            }
            if (array_key_exists('sku_model', $validated)) {
                $product->sku_model = $validated['sku_model'];
            }
            if (array_key_exists('gender', $validated)) {
                $product->gender = $validated['gender'];
            }
            if (array_key_exists('description', $validated)) {
                $product->description = $validated['description'];
            }
            if ($request->has('is_active')) {
                $product->is_active = $request->input('is_active') ? 1 : 0;
            }

            $product->save();

            // Aktualizácia obrázka
            if (!empty($validated['image_url'])) {
                // Zrušenie hlavného obrázka
                ProductImage::where('product_id', $id)->update(['is_main' => 0]);

                // Pridanie nového alebo aktualizácia
                ProductImage::updateOrCreate(
                    ['product_id' => $id, 'image_path' => $validated['image_url']],
                    ['is_main' => 1, 'sort_order' => 0]
                );
            }

            DB::commit();

            return response()->json(['ok' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'ok' => false,
                'error' => 'Update failed',
                'detail' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Vymaže produkt
     * DELETE /api/admin/products/{id}
     */
    public function apiDestroy($id)
    {
        DB::beginTransaction();

        try {
            $product = Product::findOrFail($id);

            foreach ($product->variants as $variant) {
                Inventory::where('variant_id', $variant->variant_id)->delete();
            }
            ProductVariant::where('product_id', $id)->delete();
            ProductImage::where('product_id', $id)->delete();
            $product->delete();

            DB::commit();

            return response()->json(['ok' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'ok' => false,
                'error' => 'Delete failed',
                'detail' => $e->getMessage()
            ], 500);
        }
    }
}

