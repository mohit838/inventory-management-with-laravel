<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $disk = Storage::disk('minio_private');
        
        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'subcategory_id' => $this->subcategory_id,
            'name' => $this->name,
            'sku' => $this->sku,
            'description' => $this->description,
            'image_url' => $this->image_url && method_exists($disk, 'temporaryUrl')
                ? $disk->temporaryUrl($this->image_url, now()->addMinutes(60))
                : ($this->image_url ? $disk->url($this->image_url) : null),
            'price' => (float) $this->price,
            'quantity' => (int) $this->quantity,
            'active' => (bool) $this->active,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'category' => $this->whenLoaded('category'),
            'subcategory' => $this->whenLoaded('subcategory'),
        ];
    }
}
