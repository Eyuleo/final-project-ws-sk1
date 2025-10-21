<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ResolveDisputeRequest;
use App\Models\Order;
use App\Services\OrderService;
use App\Services\EscrowService;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class DisputeController extends Controller
{
    public function __construct(
        protected OrderService $orderService,
        protected EscrowService $escrowService,
        protected NotificationService $notificationService
    ) {}

    /**
     * Display a listing of all disputes
     */
    public function index(Request $request)
    {
        $query = Order::with([
            'serviceListing',
            'studentProfile.user',
            'clientProfile.user'
        ])->where('status', 'disputed');

        // Filter by status if provided
        if ($request->has('filter')) {
            if ($request->filter === 'unresolved') {
                $query->whereNull('dispute_resolved_at');
            } elseif ($request->filter === 'resolved') {
                $query->whereNotNull('dispute_resolved_at');
            }
        }

        // Search by order number
        if ($request->has('search')) {
            $query->where('order_number', 'like', '%' . $request->search . '%');
        }

        $disputes = $query->latest('created_at')->paginate(20);

        return view('admin.disputes.index', compact('disputes'));
    }

    /**
     * Display the specified dispute
     */
    public function show(Order $order)
    {
        if ($order->status !== 'disputed') {
            return redirect()->route('admin.disputes.index')
                ->with('error', 'This order is not under dispute.');
        }

        $order->load([
            'serviceListing',
            'studentProfile.user',
            'clientProfile.user',
            'messages' => function ($query) {
                $query->orderBy('created_at', 'asc');
            },
            'transactions'
        ]);

        return view('admin.disputes.show', compact('order'));
    }

    /**
     * Resolve a dispute
     */
    public function resolve(ResolveDisputeRequest $request, Order $order)
    {
        if ($order->status !== 'disputed') {
            return redirect()->route('admin.disputes.show', $order)
                ->with('error', 'This order is not under dispute.');
        }

        $validated = $request->validated();

        try {
            $this->orderService->resolveDispute(
                $order,
                $validated['resolution'],
                $validated['admin_notes'],
                $validated['student_amount'] ?? null,
                $validated['client_amount'] ?? null
            );

            return redirect()->route('admin.disputes.index')
                ->with('success', 'Dispute resolved successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.disputes.show', $order)
                ->with('error', 'Failed to resolve dispute: ' . $e->getMessage());
        }
    }
}
