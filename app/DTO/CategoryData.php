<?php

namespace App\DTO;

class CategoryData
{
    public function __construct(
        public string $name,
        public string $slug,
        public ?string $description = null,
        public bool $active = true
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            $data['name'],
            $data['slug'],
            $data['description'] ?? null,
            isset($data['active']) ? (bool)$data['active'] : true
        );
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
