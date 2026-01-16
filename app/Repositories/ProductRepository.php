<?php

namespace App\Repositories;

use App\Models\Product;

use App\Services\RedisCacheService;

class ProductRepository extends EloquentBaseRepository
{
    protected RedisCacheService $cache;

    public function __construct(Product $model, RedisCacheService $cache)
    {
        parent::__construct($model);
        $this->cache = $cache;
    }

    public function all(array $columns = ['*'], array $relations = [])
    {
        $key = 'products.all.' . md5(json_encode([$columns, $relations]));
        return $this->cache->remember($key, 3600, fn() => parent::all($columns, $relations));
    }

    public function find(int $id, array $relations = [])
    {
        $key = "products.{$id}." . md5(json_encode($relations));
        return $this->cache->remember($key, 3600, fn() => parent::find($id, $relations));
    }

    public function create(array $data)
    {
        $this->cache->forget('products.all' . md5(json_encode([['*'], ['category', 'subcategory']])));
        return parent::create($data);
    }

    public function update(int $id, array $data)
    {
        $this->cache->forget("products.{$id}." . md5(json_encode(['category', 'subcategory'])));
        $this->cache->forget("products.{$id}." . md5(json_encode([])));
        $this->cache->forget('products.all.' . md5(json_encode([['*'], ['category', 'subcategory']])));
        return parent::update($id, $data);
    }

    public function toggleActive($id)
    {
        $item = $this->findWithInactive($id);
        $item->active = ! $item->active;
        $item->save();
        
        $this->cache->forget("products.{$id}." . md5(json_encode(['category', 'subcategory'])));
        $this->cache->forget("products.{$id}." . md5(json_encode([])));
        $this->cache->forget('products.all.' . md5(json_encode([['*'], ['category', 'subcategory']])));
        
        return $item;
    }
}
