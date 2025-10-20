<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\DeclineOrderRequest;
use App\Http\Requests\Student\UploadDeliverablesRequest;
use App\Models\Order;
use App\Services\OrderService;
use App\Services\FileUploadService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected OrderService $orderService,
        protected FileUploadService $fileUploadService
    ) {}

    /**
     * Display a listing of student's orders
     */
    public function index(Request $request)
    {
        $query = Order::where('student_profile_id', Auth::user()->studentProfile->id)
            ->with(['serviceListing', 'clientProfile.user'])
            ->latest();

        // Filter by status if provided
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->paginate(15);

        // Count orders by status for dashboard stats
        $stats = [
            'pending' => Order::where('student_profile_id', Auth::user()->studentProfile->id)
                ->where('status', 'pending')->count(),
            'in_progress' => Order::where('student_profile_id', Auth::user()->studentProfile->id)
                ->whereIn('status', ['accepted', 'in_progress'])->count(),
            'completed' => Order::where('student_profile_id', Auth::user()->studentProfile->id)
                ->where('status', 'completed')->count(),
            'revision_requested' => Order::where('student_profile_id', Auth::user()->studentProfile->id)
                ->where('status', 'revision_requested')->count(),
        ];

        return view('student.orders.index', compact('orders', 'stats'));
    }

    /**
     * Display the specified order
     */
    public function show(Order $order)
    {
        $this->authorize('view', $order);

        $order->load([
            'serviceListing',
            'clientProfile.user',
            'studentProfile.user',
            'messages.sender',
            'review'
        ]);

        return view('student.orders.show', compact('order'));
    }

    /**
     * Accept an order
     */
    public function accept(Order $order)
    {
        $this->authorize('accept', $order);

        try {
            $this->orderService->acceptOrder($order);

            return redirect()->route('student.orders.show', $order)
                ->with('success', 'Order accepted! You can now start working on it.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Decline an order with reason
     */
    public function decline(DeclineOrderRequest $request, Order $order)
    {
        $this->authorize('decline', $order);

        try {
            $this->orderService->declineOrder($order, $request->decline_reason);

            return redirect()->route('student.orders.index')
                ->with('success', 'Order declined. The client has been notified and refunded.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Update order status to in_progress
     */
    public function updateStatus(Order $order)
    {
        $this->authorize('uploadDeliverables', $order);

        try {
            $this->orderService->startWork($order);

            return redirect()->route('student.orders.show', $order)
                ->with('success', 'Order status updated to in progress.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Upload deliverables and mark order as completed
     */
    public function uploadDeliverables(UploadDeliverablesRequest $request, Order $order)
    {
        $this->authorize('uploadDeliverables', $order);

        try {
            // Upload deliverable files
            $deliverableFiles = [];
            foreach ($request->file('deliverables') as $file) {
                $deliverableFiles[] = $this->fileUploadService->uploadDeliverable($file, $order);
            }

            // Submit deliverables
            $this->orderService->submitDeliverables(
                $order,
                $deliverableFiles,
                $request->delivery_note
            );

            return redirect()->route('student.orders.show', $order)
                ->with('success', 'Deliverables uploaded successfully! The client has been notified.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }
}
