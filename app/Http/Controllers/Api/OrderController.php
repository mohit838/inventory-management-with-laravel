<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\OrderService;
use App\Http\Resources\OrderResource;
use App\Enums\PaymentMethod;
use Illuminate\Validation\Rules\Enum;

class OrderController extends Controller
{
    public function __construct(protected OrderService $service)
    {}

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email',
            'payment_method' => ['required', new Enum(PaymentMethod::class)],
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            $order = $this->service->createOrder($data, $request->user()->id);
            return (new OrderResource($order))->response()->setStatusCode(201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400); // 400 for stock issues
        }
    }

    public function invoice(int $id)
    {
        try {
            $invoice = $this->service->generateInvoice($id);
            return response()->json(['data' => $invoice]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Invoice generation failed'], 500);
        }
    }
}
