<?php

namespace App\Repositories;

use App\Models\Subcategory;

use App\Services\RedisCacheService;

class SubcategoryRepository extends EloquentBaseRepository
{
    protected RedisCacheService $cache;

    public function __construct(Subcategory $model, RedisCacheService $cache)
    {
        parent::__construct($model);
        $this->cache = $cache;
    }

    public function all(array $columns = ['*'], array $relations = [])
    {
        $key = 'subcategories.all.' . md5(json_encode([$columns, $relations]));
        return $this->cache->remember($key, 3600, fn() => parent::all($columns, $relations));
    }

    public function find(int $id, array $relations = [])
    {
        $key = "subcategories.{$id}." . md5(json_encode($relations));
        return $this->cache->remember($key, 3600, fn() => parent::find($id, $relations));
    }

    public function create(array $data)
    {
        $this->cache->forget('subcategories.all' . md5(json_encode([['*'], ['category']])));
        return parent::create($data);
    }

    public function update(int $id, array $data)
    {
        $this->cache->forget("subcategories.{$id}." . md5(json_encode(['category'])));
        $this->cache->forget("subcategories.{$id}." . md5(json_encode([])));
        $this->cache->forget('subcategories.all.' . md5(json_encode([['*'], ['category']])));
        return parent::update($id, $data);
    }

    public function toggleActive($id)
    {
        $item = $this->findWithInactive($id);
        // We need a fresh copy to save ideally, or just set property.
        // But since we are toggling, if cache is stale, toggle might be wrong.
        // Safest: Use model directly for toggle.
        $item->active = ! $item->active;
        $item->save();
        
        $this->cache->forget("subcategories.{$id}." . md5(json_encode(['category'])));
        $this->cache->forget("subcategories.{$id}." . md5(json_encode([])));
        $this->cache->forget('subcategories.all.' . md5(json_encode([['*'], ['category']])));
        
        return $item;
    }
}
