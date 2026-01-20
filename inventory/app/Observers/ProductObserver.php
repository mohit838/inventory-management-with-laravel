<?php

namespace App\Observers;

use App\Models\Product;

class ProductObserver
{
    public function __construct(protected \App\Services\AuditService $audit) {}

    /**
     * Handle the Product "created" event.
     */
    public function created(Product $product): void
    {
        $this->audit->log('product.created', "Created product '{$product->name}' (ID: {$product->id})", $product);
    }

    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        $this->audit->log('product.updated', "Updated product '{$product->name}' (ID: {$product->id})", $product);
    }

    /**
     * Handle the Product "deleted" event.
     */
    public function deleted(Product $product): void
    {
        $this->audit->log('product.deleted', "Deleted product ID: {$product->id}");
    }

    /**
     * Handle the Product "restored" event.
     */
    public function restored(Product $product): void
    {
        $this->audit->log('product.restored', "Restored product '{$product->name}' (ID: {$product->id})", $product);
    }

    /**
     * Handle the Product "force deleted" event.
     */
    public function forceDeleted(Product $product): void
    {
        $this->audit->log('product.force_deleted', "Permanently deleted product ID: {$product->id}");
    }
}
