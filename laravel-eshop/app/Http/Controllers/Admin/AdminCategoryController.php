<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

/**
 * AdminCategoryController - CRUD operácie pre kategórie v admin rozhraní
 * Fáza 4 - Kategórie
 */
class AdminCategoryController extends Controller
{
    /**
     * Zobrazí zoznam kategórií
     * GET /admin/categories
     */
    public function index(): View
    {
        $categories = Category::withCount('products')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Zobrazí formulár pre vytvorenie kategórie
     * GET /admin/categories/create
     */
    public function create(): View
    {
        return view('admin.categories.create');
    }

    /**
     * Uloží novú kategóriu
     * POST /admin/categories
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|min:2|max:100',
            'slug' => 'nullable|string|max:100|unique:categories,slug',
            'description' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer|min:0',
        ], [
            'name.required' => 'Názov kategórie je povinný',
            'name.min' => 'Názov musí mať aspoň 2 znaky',
            'slug.unique' => 'Tento slug už existuje',
        ]);

        $category = new Category();
        $category->name = $validated['name'];
        $category->slug = $validated['slug'] ?? Str::slug($validated['name']);
        $category->description = $validated['description'] ?? null;
        $category->sort_order = $validated['sort_order'] ?? 0;
        $category->is_active = $request->has('is_active') ? 1 : 0;
        $category->save();

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Kategória bola úspešne vytvorená');
    }

    /**
     * Zobrazí formulár pre úpravu kategórie
     * GET /admin/categories/{id}/edit
     */
    public function edit($id): View
    {
        $category = Category::findOrFail($id);
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Aktualizuje kategóriu
     * PUT /admin/categories/{id}
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|min:2|max:100',
            'slug' => 'nullable|string|max:100|unique:categories,slug,' . $id . ',category_id',
            'description' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $category->name = $validated['name'];
        $category->slug = $validated['slug'] ?? Str::slug($validated['name']);
        $category->description = $validated['description'] ?? null;
        $category->sort_order = $validated['sort_order'] ?? 0;
        $category->is_active = $request->has('is_active') ? 1 : 0;
        $category->save();

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Kategória bola úspešne aktualizovaná');
    }

    /**
     * Vymaže kategóriu
     * DELETE /admin/categories/{id}
     */
    public function destroy($id): RedirectResponse
    {
        $category = Category::findOrFail($id);

        // Kontrola či kategória nemá produkty
        if ($category->products()->count() > 0) {
            return back()->with('error', 'Kategória obsahuje produkty a nemôže byť vymazaná. Najprv presuňte produkty do inej kategórie.');
        }

        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Kategória bola vymazaná');
    }
}
