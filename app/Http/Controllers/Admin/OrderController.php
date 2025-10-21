<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of all orders
     */
    public function index(Request $request)
    {
        $query = Order::with([
            'serviceListing',
            'studentProfile.user',
            'clientProfile.user'
        ]);

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Search by order number, client name, or student name
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhereHas('clientProfile.user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('studentProfile.user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Sort by column
        $sortBy = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        
        $query->orderBy($sortBy, $sortDirection);

        $orders = $query->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Display the specified order
     */
    public function show(Order $order)
    {
        $order->load([
            'serviceListing',
            'studentProfile.user',
            'clientProfile.user',
            'messages.sender',
            'transactions',
            'review'
        ]);

        return view('admin.orders.show', compact('order'));
    }
}
