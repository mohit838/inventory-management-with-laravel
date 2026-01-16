<?php

namespace App\DTO;

class SubcategoryData
{
    public function __construct(
        public int $category_id,
        public string $name,
        public string $slug,
        public bool $active = true
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            (int)$data['category_id'],
            $data['name'],
            $data['slug'],
            isset($data['active']) ? (bool)$data['active'] : true
        );
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
