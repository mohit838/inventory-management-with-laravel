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
    public function __construct(protected ProductRepository $repo, protected \App\Services\MinioService $minio)
    {}

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', \App\Constants\GlobalConstants::PER_PAGE_DEFAULT);
        $search = $request->input('search');
        
        if ($search) {
             $items = $this->repo->search($search, ['name', 'sku', 'description'], $perPage, ['category', 'subcategory']);
        } else {
             $items = $this->repo->paginate($perPage, ['*'], ['category', 'subcategory']);
        }

        return ProductResource::collection($items);
    }

    public function dropdown()
    {
        $items = $this->repo->all(['id', 'name', 'sku']);
        return response()->json($items);
    }

    public function store(ProductStoreRequest $request)
    {
        $data = $request->validated();
        
        if ($request->hasFile('image')) {
            $user = $request->user();
            $url = $this->minio->uploadImage($request->file('image'), $user->id);
            $data['image_url'] = $url;
        }

        $dto = \App\DTO\ProductData::fromArray($data);
        $item = $this->repo->create($dto->toArray());
        return (new ProductResource($item))->response()->setStatusCode(201);
    }

    public function show($id)
    {
        $item = $this->repo->findWithInactive((int)$id);
        return new ProductResource($item);
    }

    public function update(ProductUpdateRequest $request, $id)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $user = $request->user();
            $url = $this->minio->uploadImage($request->file('image'), $user->id);
            $data['image_url'] = $url;
        }

        $dto = \App\DTO\ProductData::fromArray($data);
        $item = $this->repo->update((int)$id, $dto->toArray());
        return new ProductResource($item);
    }

    public function toggleActive(Request $request, $id)
    {
        $item = $this->repo->toggleActive((int)$id);
        return new ProductResource($item);
    }

    public function destroy($id)
    {
        $this->repo->delete((int)$id);
        return response()->json(null, 204);
    }
}
