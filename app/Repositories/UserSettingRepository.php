<?php

namespace App\Repositories;

use App\Models\UserSetting;
use Illuminate\Database\Eloquent\Model;

class UserSettingRepository extends EloquentBaseRepository
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
