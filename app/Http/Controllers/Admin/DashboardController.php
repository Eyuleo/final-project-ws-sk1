<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\ServiceListing;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard
     */
    public function index()
    {
        // Get key statistics
        $stats = [
            'total_users' => User::whereIn('role', ['student', 'client'])->count(),
            'total_students' => User::where('role', 'student')->count(),
            'total_clients' => User::where('role', 'client')->count(),
            'total_services' => ServiceListing::count(),
            'active_services' => ServiceListing::where('status', 'active')->count(),
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'disputed_orders' => Order::where('status', 'disputed')->count(),
            'completed_orders' => Order::where('status', 'approved')->count(),
            'total_revenue' => Transaction::where('type', 'platform_fee')
                ->where('status', 'completed')
                ->sum('amount'),
        ];

        // Recent orders
        $recentOrders = Order::with([
            'serviceListing',
            'studentProfile.user',
            'clientProfile.user'
        ])
            ->latest()
            ->take(10)
            ->get();

        // Disputed orders requiring attention
        $disputedOrders = Order::with([
            'serviceListing',
            'studentProfile.user',
            'clientProfile.user'
        ])
            ->where('status', 'disputed')
            ->latest()
            ->get();

        // Revenue trend (last 7 days)
        $revenueTrend = Transaction::where('type', 'platform_fee')
            ->where('status', 'completed')
            ->where('created_at', '>=', now()->subDays(7))
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'recentOrders',
            'disputedOrders',
            'revenueTrend'
        ));
    }
}
