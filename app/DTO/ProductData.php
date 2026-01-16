<?php

namespace App\DTO;

class ProductData
{
    public function __construct(
        public int $category_id,
        public ?int $subcategory_id,
        public string $name,
        public string $sku,
        public ?string $description = null,
        public float $price = 0.0,
        public int $quantity = 0,
        public bool $active = true
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            (int)$data['category_id'],
            isset($data['subcategory_id']) ? (int)$data['subcategory_id'] : null,
            $data['name'],
            $data['sku'],
            $data['description'] ?? null,
            isset($data['price']) ? (float)$data['price'] : 0.0,
            isset($data['quantity']) ? (int)$data['quantity'] : 0,
            isset($data['active']) ? (bool)$data['active'] : true
        );
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
