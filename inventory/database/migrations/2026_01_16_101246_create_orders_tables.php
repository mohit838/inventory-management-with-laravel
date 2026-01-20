<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // Who placed it (SaaS user)
            $table->string('customer_name');
            $table->string('customer_email')->nullable();

            $table->decimal('total_amount', 12, 2);
            $table->string('payment_method'); // Enum: COD, ONLINE
            $table->string('payment_status')->default('pending'); // Enum: PENDING, PAID, FAILED
            $table->string('status')->default('pending'); // Enum: PENDING, COMPLETED, CANCELLED

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('set null');

            $table->integer('quantity');
            $table->decimal('unit_price', 12, 2); // Snapshot price
            $table->decimal('total_price', 12, 2); // quantity * unit_price

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
