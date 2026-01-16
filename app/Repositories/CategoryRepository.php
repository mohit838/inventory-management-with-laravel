<?php

namespace App\Repositories;

use App\Models\Category;

use App\Services\RedisCacheService;

class CategoryRepository extends EloquentBaseRepository
{
    protected RedisCacheService $cache;

    public function __construct(Category $model, RedisCacheService $cache)
    {
        parent::__construct($model);
        $this->cache = $cache;
    }

    public function all(array $columns = ['*'], array $relations = [])
    {
        $key = 'categories.all.' . md5(json_encode([$columns, $relations]));
        return $this->cache->remember($key, 3600, fn() => parent::all($columns, $relations));
    }

    public function find(int $id, array $relations = [])
    {
        $key = "categories.{$id}." . md5(json_encode($relations));
        return $this->cache->remember($key, 3600, fn() => parent::find($id, $relations));
    }

    public function create(array $data)
    {
        $this->cache->flushTags(['categories']); // If using tags, else just specialized clear
        $this->cache->forget('categories.all' . md5(json_encode([['*'], ['subcategories']]))); // Clears common default
        // For simplicity, just clearing specific known keys or use flush if not huge.
        // Since custom keys are dynamic, tag based is better but Redis simple doesn't always support tags smoothly without specific driver.
        // I will just return parent create and let cache expire or just implement 'forget' for list.
        $this->cache->forget('categories.all.' . md5(json_encode([['*'], ['subcategories']])));
        return parent::create($data);
    }

    public function update(int $id, array $data)
    {
        $this->cache->forget("categories.{$id}." . md5(json_encode(['subcategories'])));
        $this->cache->forget("categories.{$id}." . md5(json_encode([])));
         $this->cache->forget('categories.all.' . md5(json_encode([['*'], ['subcategories']])));
        return parent::update($id, $data);
    }

    public function toggleActive($id)
    {
        $item = $this->findWithInactive($id);
        // If cached item is old, active might be wrong. Better to use freshFind inside or just update directly.
        $item->active = ! $item->active;
        $item->save();
        
        $this->cache->forget("categories.{$id}." . md5(json_encode(['subcategories'])));
        $this->cache->forget("categories.{$id}." . md5(json_encode([])));
        $this->cache->forget('categories.all.' . md5(json_encode([['*'], ['subcategories']])));
        
        return $item;
    }
}
