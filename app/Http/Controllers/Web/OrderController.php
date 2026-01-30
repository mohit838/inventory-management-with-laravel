<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\PaymentMethod;

class OrderController extends Controller
{
    protected OrderRepository $orderRepository;
    protected ProductRepository $productRepository;
    protected InvoiceService $invoiceService;

    public function __construct(
        OrderRepository $orderRepository,
        ProductRepository $productRepository,
        InvoiceService $invoiceService
    ) {
        $this->orderRepository = $orderRepository;
        $this->productRepository = $productRepository;
        $this->invoiceService = $invoiceService;
    }

    /**
     * Display a listing of orders
     */
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $status = $request->get('status', '');

        $query = Order::query();

        if ($search) {
            $query->where('id', 'like', "%{$search}%");
        }

        if ($status) {
            $query->where('status', $status);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('orders.index', compact('orders', 'search', 'status'));
    }

    /**
     * Show the form for creating a new order
     */
    public function create()
    {
        $products = Product::where('active', true)->where('quantity', '>', 0)->get();

        return view('orders.create', compact('products'));
    }

    /**
     * Store a newly created order
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'payment_method' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        return DB::transaction(function () use ($validated) {
            $totalAmount = 0;
            $itemsData = [];

            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                
                // Check stock
                if ($product->quantity < $item['quantity']) {
                    return back()->withErrors(['items' => "Not enough stock for {$product->name}"]);
                }

                $itemTotal = $product->price * $item['quantity'];
                $totalAmount += $itemTotal;

                $itemsData[] = [
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price,
                    'total_price' => $itemTotal,
                ];

                // Deduct stock
                $product->decrement('quantity', $item['quantity']);
            }

            $orderData = [
                'user_id' => auth()->id(),
                'customer_name' => $validated['customer_name'],
                'customer_email' => $validated['customer_email'],
                'total_amount' => $totalAmount,
                'payment_method' => $validated['payment_method'],
                'payment_status' => 'pending',
                'status' => 'pending',
                'active' => true,
            ];

            $order = $this->orderRepository->createOrderWithItems($orderData, $itemsData);

            return redirect()->route('orders.show', $order->id)
                ->with('success', 'Order created successfully.');
        });
    }

    /**
     * Display the specified order
     */
    public function show($id)
    {
        $order = $this->orderRepository->findWithItems($id);

        return view('orders.show', compact('order'));
    }

    /**
     * Download order invoice
     */
    public function invoice($id)
    {
        $order = $this->orderRepository->findWithItems($id);
        $invoiceData = $this->invoiceService->generateInvoice($order);

        return redirect($invoiceData['url']);
    }

    /**
     * Toggle order active status (archive/unarchive)
     */
    public function toggleActive($id)
    {
        $order = Order::findOrFail($id);
        $order->active = !$order->active;
        $order->save();

        $status = $order->active ? 'unarchived' : 'archived';

        return redirect()->route('orders.index')
            ->with('success', "Order {$status} successfully.");
    }
}
