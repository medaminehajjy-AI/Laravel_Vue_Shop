<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function stats()
    {
        $stats = [
            'products_count' => Product::count(),
            'users_count' => User::count(),
            'orders_count' => Order::count(),
            'revenue' => Order::where(function ($query) {
                  $query->where('status', 'paid')
                        ->orWhere('status', 'shipped')
                        ->orWhere('status', 'delivered');
                })->sum('total_price'),
        ];

        return response()->json($stats);
    }

    public function orders()
    {
        $orders = Order::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($orders);
    }

    public function updateOrderStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,paid,shipped,delivered',
        ]);

        $order->update(['status' => $validated['status']]);

        return response()->json($order);
    }

    public function unreadCount()
    {
        $count = Order::where('is_read', false)->count();
        
        return response()->json(['count' => $count]);
    }

    public function markOrdersAsRead()
    {
        $updated = Order::where('is_read', false)->update(['is_read' => true]);
        
        return response()->json([
            'message' => 'Orders marked as read',
            'updated_count' => $updated
        ]);
    }

        public function revenueChart()
        {
            $months = collect();

            for ($i = 5; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);

                $total = Order::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->where('status', '!=', 'pending')
                    ->sum('total_price');

                $months->push([
                    'date' => $date->format('Y-m'),
                    'total' => $total
                ]);
            }

            return response()->json($months);
        }
        public function recentOrders()
        {
            $orders = Order::with('user')
                ->latest()
                ->take(5)
                ->get()
                ->map(function ($order) {
                    return [
                        'id' => $order->id,
                        'user' => $order->user->name ?? 'Guest',
                        'total' => $order->total_price,
                        'status' => $order->status,
                        'date' => $order->created_at->format('Y-m-d H:i'),
                    ];
                });

            return response()->json($orders);
        }

        // this function will be available in the future
        public function topProducts()
        {
            $products = OrderItem::with('product')
                ->selectRaw('product_id, SUM(quantity) as total_sold')
                ->groupBy('product_id')
                ->orderByDesc('total_sold')
                ->take(5)
                ->get()
                ->map(function ($item) {
                    return [
                        'name' => $item->product->name ?? 'Unknown',
                        'total' => $item->total_sold,
                    ];
                });

            return response()->json($products);
        }
}
