<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\PlaceOrderRequest;
use App\Http\Requests\Client\RequestRevisionRequest;
use App\Http\Requests\Client\DisputeOrderRequest;
use App\Models\Order;
use App\Models\ServiceListing;
use App\Services\OrderService;
use App\Services\PaymentService;
use App\Services\FileUploadService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected OrderService $orderService,
        protected PaymentService $paymentService,
        protected FileUploadService $fileUploadService
    ) {}

    /**
     * Display a listing of client's orders
     */
    public function index(Request $request)
    {
        $query = Order::where('client_profile_id', Auth::user()->clientProfile->id)
            ->with(['serviceListing', 'studentProfile.user'])
            ->latest();

        // Filter by status if provided
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->paginate(15);

        return view('client.orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new order
     */
    public function create(ServiceListing $service)
    {
        if ($service->status !== 'active') {
            return redirect()->back()->with('error', 'This service is not currently available.');
        }

        return view('client.orders.create', compact('service'));
    }

    /**
     * Store a newly created order and redirect to Stripe Checkout
     */
    public function store(PlaceOrderRequest $request)
    {
        $service = ServiceListing::findOrFail($request->service_listing_id);
        $client = Auth::user()->clientProfile;

        // Handle attachment files upload
        $attachmentFiles = null;
        if ($request->hasFile('attachment_files')) {
            $attachmentFiles = [];
            foreach ($request->file('attachment_files') as $file) {
                $attachmentFiles[] = $this->fileUploadService->uploadOrderAttachment($file);
            }
        }

        // Create order
        $order = $this->orderService->createOrder($service, $client, [
            'requirements' => $request->requirements,
            'quantity' => $request->quantity,
            'deadline' => $request->deadline,
            'attachment_files' => $attachmentFiles,
        ]);

        // Create Stripe Checkout session
        try {
            $session = $this->paymentService->createCheckoutSession($order);
            
            return redirect($session->url);
        } catch (\Exception $e) {
            // Delete order if payment session creation fails
            $order->delete();
            
            // Log the actual error for debugging
            \Log::error('Stripe Checkout session creation failed', [
                'error' => $e->getMessage(),
                'order_id' => $order->id,
            ]);
            
            return redirect()->back()
                ->with('error', 'Unable to process payment: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Handle successful payment callback from Stripe
     */
    public function success(Request $request)
    {
        $sessionId = $request->query('session_id');
        
        if (!$sessionId) {
            return redirect()->route('client.dashboard')
                ->with('error', 'Invalid payment session.');
        }

        // Find order by session ID
        $order = Order::where('stripe_session_id', $sessionId)->first();

        // If not found by session ID, try to retrieve from Stripe and find by order_id in metadata
        if (!$order) {
            try {
                $session = \Stripe\Checkout\Session::retrieve($sessionId);
                $orderId = $session->metadata->order_id ?? $session->client_reference_id;
                
                if ($orderId) {
                    $order = Order::find($orderId);
                    
                    // Update the order with session ID if found
                    if ($order) {
                        $order->update(['stripe_session_id' => $sessionId]);
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Failed to retrieve Stripe session', [
                    'session_id' => $sessionId,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        if (!$order) {
            return redirect()->route('client.dashboard')
                ->with('error', 'Order not found. Please check your orders page.');
        }

        return view('client.orders.success', compact('order'));
    }

    /**
     * Handle cancelled payment callback
     */
    public function cancel(Request $request)
    {
        return redirect()->route('client.dashboard')
            ->with('warning', 'Payment was cancelled. You can try again anytime.');
    }

    /**
     * Display the specified order
     */
    public function show(Order $order)
    {
        $this->authorize('view', $order);

        $order->load([
            'serviceListing',
            'studentProfile.user',
            'clientProfile.user',
            'messages.sender',
            'review'
        ]);

        return view('client.orders.show', compact('order'));
    }

    /**
     * Approve completed order and release escrow
     */
    public function approve(Order $order)
    {
        $this->authorize('approve', $order);

        try {
            $this->orderService->approveOrder($order);

            return redirect()->route('client.orders.show', $order)
                ->with('success', 'Order approved! Payment has been released to the student.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Request revision on completed order
     */
    public function requestRevision(RequestRevisionRequest $request, Order $order)
    {
        try {
            $this->orderService->requestRevision($order, $request->revision_notes);

            return redirect()->route('client.orders.show', $order)
                ->with('success', 'Revision requested. The student has been notified.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Open dispute for order
     */
    public function dispute(DisputeOrderRequest $request, Order $order)
    {
        // Handle evidence files upload
        $evidenceFiles = [];
        if ($request->hasFile('evidence_files')) {
            foreach ($request->file('evidence_files') as $file) {
                $evidenceFiles[] = $this->fileUploadService->uploadDisputeEvidence($file);
            }
        }

        try {
            $this->orderService->openDispute($order, $request->reason, $evidenceFiles);

            return redirect()->route('client.orders.show', $order)
                ->with('success', 'Dispute opened. An admin will review your case.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }
}
