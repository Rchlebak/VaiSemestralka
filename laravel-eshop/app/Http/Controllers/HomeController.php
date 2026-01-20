<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Show the application landing page.
     */
    public function index(): View
    {
        // 1. Featured Products (Latest 8 active products)
        $featuredProducts = Product::active()
            ->with(['images'])
            ->orderBy('product_id', 'desc')
            ->limit(8)
            ->get();

        // 2. Categories for the grid (Tenisky, Doplnky)
        // We will fetch their IDs or Slugs to build links
        $categories = Category::whereIn('name', ['Tenisky', 'Doplnky'])->get()->keyBy('name');

        return view('home', compact('featuredProducts', 'categories'));
    }
}
