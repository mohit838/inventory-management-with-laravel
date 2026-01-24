<?php

namespace App\Observers;

use App\Models\Category;
use App\Services\AuditService;

class CategoryObserver
{
    public function __construct(protected AuditService $audit) {}

    public function created(Category $category): void
    {
        $this->audit->log('category.created', "Created category '{$category->name}' (ID: {$category->id})", $category);
    }

    public function updated(Category $category): void
    {
        $this->audit->log('category.updated', "Updated category '{$category->name}' (ID: {$category->id})", $category);
    }

    public function deleted(Category $category): void
    {
        $this->audit->log('category.deleted', "Deleted category ID: {$category->id}");
    }
}
