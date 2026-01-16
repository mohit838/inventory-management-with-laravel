<?php

namespace App\DTO;

class LoginData
{
    public function __construct(
        public string $email,
        public string $password
    ) {}

    public static function fromRequest($request): self
    {
        return new self(
            $request->input('email'),
            $request->input('password')
        );
    }
}
