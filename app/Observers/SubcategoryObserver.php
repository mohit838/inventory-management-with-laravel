<?php

namespace App\Observers;

use App\Models\Subcategory;
use App\Services\AuditService;

class SubcategoryObserver
{
    public function __construct(protected AuditService $audit) {}

    public function created(Subcategory $subcategory): void
    {
        $this->audit->log('subcategory.created', "Created subcategory '{$subcategory->name}' (ID: {$subcategory->id})", $subcategory);
    }

    public function updated(Subcategory $subcategory): void
    {
        $this->audit->log('subcategory.updated', "Updated subcategory '{$subcategory->name}' (ID: {$subcategory->id})", $subcategory);
    }

    public function deleted(Subcategory $subcategory): void
    {
        $this->audit->log('subcategory.deleted', "Deleted subcategory ID: {$subcategory->id}");
    }
}
