<?php

namespace App\Services;

use App\Interfaces\InvoiceGeneratorInterface;
use App\Models\Order;

class BasicJsonInvoiceGenerator implements InvoiceGeneratorInterface
{
    public function generate(Order $order): array
    {
        return [
            'invoice_number' => 'INV-'.str_pad($order->id, 6, '0', STR_PAD_LEFT),
            'date' => $order->created_at->toIso8601String(),
            'customer' => [
                'name' => $order->customer_name,
                'email' => $order->customer_email,
            ],
            'items' => $order->items->map(function ($item) {
                return [
                    'product_id' => $item->product_id,
                    'description' => $item->product->name ?? 'Unknown Product',
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'total' => $item->total_price,
                ];
            }),
            'total_amount' => $order->total_amount,
            'payment_method' => $order->payment_method->value,
            'status' => $order->status->value,
        ];
    }
}
