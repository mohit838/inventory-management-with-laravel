<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id', 'customer_name', 'customer_email',
        'total_amount', 'payment_method', 'payment_status', 'status',
    ];

    protected $casts = [
        'status' => \App\Enums\OrderStatus::class,
        'payment_method' => \App\Enums\PaymentMethod::class,
        'payment_status' => \App\Enums\PaymentStatus::class,
        'total_amount' => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
