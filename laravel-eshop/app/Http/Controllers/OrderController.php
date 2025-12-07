<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

/**
 * OrderController - spracovanie objednávok
 */
class OrderController extends Controller
{
    /**
     * Zobrazí košík / checkout stránku
     * GET /checkout
     */
    public function checkout(): View
    {
        return view('checkout');
    }

    /**
     * Vytvorí objednávku
     * POST /checkout
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|min:2|max:200',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'nullable|string|max:40',
            'ship_street' => 'required|string|min:5|max:200',
            'ship_city' => 'required|string|min:2|max:200',
            'ship_zip' => 'required|string|min:4|max:20',
            'ship_country' => 'required|string|max:20',
            'cart' => 'required|array|min:1',
            'cart.*.productId' => 'required|numeric',
            'cart.*.variantId' => 'nullable|numeric',
            'cart.*.qty' => 'required|integer|min:1',
            'cart.*.price' => 'required|numeric|min:0',
        ], [
            'customer_name.required' => 'Meno je povinné',
            'customer_email.required' => 'Email je povinný',
            'customer_email.email' => 'Neplatný email',
            'ship_street.required' => 'Ulica je povinná',
            'ship_city.required' => 'Mesto je povinné',
            'ship_zip.required' => 'PSČ je povinné',
            'cart.required' => 'Košík je prázdny',
        ]);

        DB::beginTransaction();

        try {
            // Výpočet celkovej sumy
            $total = 0;
            foreach ($validated['cart'] as $item) {
                $total += $item['price'] * $item['qty'];
            }

            // Vytvorenie objednávky
            $order = new Order();
            $order->order_id = Order::generateNewId();
            $order->email = $validated['customer_email'];
            $order->status = Order::STATUS_PENDING;
            $order->total_amount = $total;
            $order->ship_name = $validated['customer_name'];
            $order->ship_street = $validated['ship_street'];
            $order->ship_city = $validated['ship_city'];
            $order->ship_zip = $validated['ship_zip'];
            $order->ship_country = $validated['ship_country'];
            $order->save();

            // Pridanie položiek
            foreach ($validated['cart'] as $item) {
                $orderItem = new OrderItem();
                $orderItem->order_item_id = OrderItem::generateNewId();
                $orderItem->order_id = $order->order_id;
                $orderItem->variant_id = $item['variantId'] ?? null;
                $orderItem->qty = $item['qty'];
                $orderItem->unit_price = $item['price'];
                $orderItem->line_total = $item['qty'] * $item['price'];
                $orderItem->save();

                // Zníženie skladu
                if (!empty($item['variantId'])) {
                    $inventory = Inventory::find($item['variantId']);
                    if ($inventory) {
                        $inventory->decreaseStock($item['qty']);
                    }
                }
            }

            DB::commit();

            return redirect()
                ->route('order.success', ['id' => $order->order_id])
                ->with('success', 'Objednávka bola úspešne vytvorená');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Chyba pri vytváraní objednávky: ' . $e->getMessage());
        }
    }

    /**
     * Zobrazí potvrdenie objednávky
     * GET /order/success/{id}
     */
    public function success($id): View
    {
        $order = Order::with(['items.variant.product'])->findOrFail($id);
        return view('order-success', compact('order'));
    }

    /**
     * API: Vytvorí objednávku
     * POST /api/orders
     */
    public function apiStore(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|min:2|max:200',
            'customer_email' => 'required|email|max:255',
            'customer_address' => 'required|string|min:10',
            'cart' => 'required|array|min:1',
            'cart.*.productId' => 'required|numeric',
            'cart.*.variantId' => 'nullable|numeric',
            'cart.*.qty' => 'required|integer|min:1',
            'cart.*.price' => 'required|numeric|min:0',
        ], [
            'customer_name.required' => 'Meno je povinné',
            'customer_email.required' => 'Email je povinný',
            'customer_email.email' => 'Neplatný email',
            'customer_address.required' => 'Adresa je povinná',
            'customer_address.min' => 'Adresu zadajte podrobne',
            'cart.required' => 'Košík je prázdny',
        ]);

        DB::beginTransaction();

        try {
            $total = 0;
            foreach ($validated['cart'] as $item) {
                $total += $item['price'] * $item['qty'];
            }

            $order = new Order();
            $order->order_id = Order::generateNewId();
            $order->email = $validated['customer_email'];
            $order->status = Order::STATUS_PENDING;
            $order->total_amount = $total;
            $order->ship_name = $validated['customer_name'];
            $order->ship_street = $validated['customer_address'];
            $order->ship_city = '';
            $order->ship_zip = '';
            $order->ship_country = 'SK';
            $order->save();

            foreach ($validated['cart'] as $item) {
                $orderItem = new OrderItem();
                $orderItem->order_item_id = OrderItem::generateNewId();
                $orderItem->order_id = $order->order_id;
                $orderItem->variant_id = $item['variantId'] ?? null;
                $orderItem->qty = $item['qty'];
                $orderItem->unit_price = $item['price'];
                $orderItem->line_total = $item['qty'] * $item['price'];
                $orderItem->save();

                if (!empty($item['variantId'])) {
                    $inventory = Inventory::find($item['variantId']);
                    if ($inventory) {
                        $inventory->decreaseStock($item['qty']);
                    }
                }
            }

            DB::commit();

            $orderNumber = 'ORD' . str_pad($order->order_id, 6, '0', STR_PAD_LEFT);

            return response()->json([
                'ok' => true,
                'order_id' => $order->order_id,
                'order_number' => $orderNumber,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'ok' => false,
                'error' => 'Could not create order',
                'detail' => $e->getMessage()
            ], 500);
        }
    }
}

