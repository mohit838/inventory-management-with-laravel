<?php

namespace App\Repositories;

use App\Interfaces\BaseRepositoryInterface;
use App\Models\UserSetting;

class UserSettingRepository extends EloquentBaseRepository implements BaseRepositoryInterface
{
    public function __construct(UserSetting $model)
    {
        parent::__construct($model);
    }

    public function getByKey(int $userId, string $key)
    {
        return $this->model->where('user_id', $userId)->where('key', $key)->first();
    }

    public function set(int $userId, string $key, $value)
    {
        return $this->model->updateOrCreate(
            ['user_id' => $userId, 'key' => $key],
            ['value' => $value]
        );
    }

    public function getAllForUser(int $userId)
    {
        return $this->model->where('user_id', $userId)->get();
    }
}
