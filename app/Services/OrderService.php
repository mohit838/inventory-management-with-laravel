<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Product;
use App\Repositories\OrderRepository;
use App\Services\InvoiceService;
use Exception;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(
        protected OrderRepository $orderRepo,
        protected InvoiceService $invoiceGenerator
    ) {}

    /**
     * Create a new order with stock validation and deduction.
     */
    public function createOrder(array $data, int $userId): \App\Models\Order
    {
        return DB::transaction(function () use ($data, $userId) {
            $itemsData = [];
            $totalAmount = 0;

            // 1. Validate Stock and Prepare Items
            foreach ($data['items'] as $item) {
                $product = Product::lockForUpdate()->find($item['product_id']); // Lock row

                if (! $product) {
                    throw new Exception("Product ID {$item['product_id']} not found.");
                }

                if ($product->quantity < $item['quantity']) {
                    throw new Exception("Insufficient stock for Product '{$product->name}'. Available: {$product->quantity}, Requested: {$item['quantity']}");
                }

                // 2. Deduct Stock
                $product->quantity -= $item['quantity'];
                $product->save();

                // 3. Prepare Item Data (Snapshot Price)
                $lineTotal = $product->price * $item['quantity'];
                $totalAmount += $lineTotal;

                $itemsData[] = [
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price,
                    'total_price' => $lineTotal,
                ];
            }

            // 4. Create Order
            $orderData = [
                'user_id' => $userId,
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'] ?? null,
                'total_amount' => $totalAmount,
                'payment_method' => $data['payment_method'],
                'payment_status' => PaymentStatus::PENDING, // Default
                'status' => OrderStatus::PENDING,
            ];

            return $this->orderRepo->createOrderWithItems($orderData, $itemsData);
        });
    }

    public function generateInvoice(int $orderId)
    {
        $order = $this->orderRepo->find($orderId, ['items.product']);

        return $this->invoiceGenerator->generate($order);
    }
}
