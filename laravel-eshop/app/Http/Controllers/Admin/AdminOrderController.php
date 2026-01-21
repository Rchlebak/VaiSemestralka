<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

/**
 * AdminOrderController - správa objednávok v admin paneli
 */
class AdminOrderController extends Controller
{
    /**
     * Zobrazí zoznam všetkých objednávok
     */
    public function index(): View
    {
        $orders = Order::with(['items.variant.product', 'user'])
            ->orderBy('order_id', 'desc')
            ->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Zobrazí detail objednávky
     */
    public function show($id): View
    {
        $order = Order::with(['items.variant.product.images', 'user'])
            ->findOrFail($id);

        return view('admin.orders.show', compact('order'));
    }
}
