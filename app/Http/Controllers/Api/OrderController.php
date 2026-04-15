<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::with('items.product')
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($orders);
    }

    public function checkout(Request $request)
    {
        $request->validate([
        'phone' => 'required|string',
        'shipping_address' => 'required|string',
        'cart_items' => 'required|array',
        'total_amount' => 'required|numeric',
        'payment_method' => 'required|in:cod,online',
    ]);
        $cartItems = Cart::with('product')
            ->where('user_id', $request->user()->id)
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Cart is empty'], 400);
        }

        try {
            $order = DB::transaction(function () use ($request, $cartItems) {
                $totalPrice = $cartItems->sum(function ($item) {
                    return $item->product->price * $item->quantity;
                });

                $order = Order::create([
                    'user_id' => $request->user()->id,
                    'total_price' => $totalPrice,

                    'phone' => $request->phone,
                    'shipping_address' => $request->shipping_address,
                    'notes' => $request->notes,
                    'payment_method' => $request->payment_method,
                    'status' => $request->payment_method === 'cod' ? 'pending' : 'paid',
                    'is_read' => 0,
                    
                ]);

                foreach ($cartItems as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                        'price' => $item->product->price,
                    ]);
                }

                Cart::where('user_id', $request->user()->id)->delete();

                return $order;
            });

            return response()->json([
                'message' => 'Order placed successfully',
                'order' => $order
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Checkout failed: ' . $e->getMessage()], 500);
        }
    }

    public function show(Request $request, Order $order)
    {
        if ($order->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $order->load('items.product');

        return response()->json($order);
    }
}
