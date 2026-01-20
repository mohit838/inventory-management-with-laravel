<?php

namespace App\Notifications;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class LowStockAlert extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Product $product, public int $threshold) {}

    public function via(object $notifiable): array
    {
        return ['database']; // Can add 'mail' later clearly
    }

    public function toArray(object $notifiable): array
    {
        return [
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'quantity' => $this->product->quantity,
            'threshold' => $this->threshold,
            'message' => "Product '{$this->product->name}' is low on stock ({$this->product->quantity} left, threshold: {$this->threshold}).",
        ];
    }
}
