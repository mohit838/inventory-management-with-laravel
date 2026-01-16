<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\ProductRepository;
use App\Models\Product;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Resources\ProductResource;

class ProductController extends Controller
{
    public function __construct(protected ProductRepository $repo)
    {}

    public function index(Request $request)
    {
        $items = $this->repo->all(['*'], ['category', 'subcategory']);
        return response()->json(ProductResource::collection($items));
    }

    public function store(ProductStoreRequest $request)
    {
        $dto = \App\DTO\ProductData::fromArray($request->validated());
        $item = $this->repo->create($dto->toArray());
        return response()->json(new ProductResource($item), 201);
    }

    public function show($id)
    {
        $item = $this->repo->find((int)$id, ['category', 'subcategory']);
        return response()->json(new ProductResource($item));
    }

    public function update(ProductUpdateRequest $request, $id)
    {
        $dto = \App\DTO\ProductData::fromArray($request->validated());
        $item = $this->repo->update((int)$id, $dto->toArray());
        return response()->json(new ProductResource($item));
    }

    public function toggleActive(Request $request, $id)
    {
        $item = $this->repo->toggleActive((int)$id);
        return response()->json(new ProductResource($item));
    }

    public function destroy($id)
    {
        $this->repo->delete((int)$id);
        return response()->json(null, 204);
    }
}
