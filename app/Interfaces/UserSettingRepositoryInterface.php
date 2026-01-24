<?php

namespace App\Interfaces;

interface UserSettingRepositoryInterface extends BaseRepositoryInterface
{
    public function getByKey(int $userId, string $key);

    public function set(int $userId, string $key, $value);

    public function getAllForUser(int $userId);
}
