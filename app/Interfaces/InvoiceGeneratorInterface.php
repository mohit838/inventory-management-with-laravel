<?php

namespace App\Interfaces;

use App\Models\Order;

interface InvoiceGeneratorInterface
{
    /**
     * Generate invoice data for an order.
     * Can return array of data, or binary stream PDF, etc. 
     * For now, returning array structure.
     */
    public function generate(Order $order): array;
}
