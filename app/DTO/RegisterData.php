<?php

namespace App\DTO;

class RegisterData
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public string $role = 'user'
    ) {}

    public static function fromRequest($request): self
    {
        return new self(
            $request->input('name'),
            $request->input('email'),
            $request->input('password'),
            $request->input('role', 'user')
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'role' => $this->role,
        ];
    }
}
