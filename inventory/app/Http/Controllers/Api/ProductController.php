<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Resources\ProductResource;
use App\Repositories\ProductRepository;
use App\Services\MinioService;
use App\Traits\PaginationTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    use PaginationTrait;

    public function __construct(
        protected ProductRepository $repo,
        protected MinioService $minio
    ) {}

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);
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

        return response()->json([
            'data' => $items->map(fn($item) => [
                'id' => $item->id,
                'name' => $item->name,
                'sku' => $item->sku
            ])
        ]);
    }

    public function store(ProductStoreRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $user = $request->user();
            $data['image_url'] = $this->minio->uploadImage($request->file('image'), $user ? $user->id : 0);
        }

        $item = $this->repo->create($data);

        Log::info("Product created: {$item->id}");

        return new ProductResource($item);
    }

    public function show($id)
    {
        $item = $this->repo->findWithInactive((int) $id);

        return new ProductResource($item);
    }

    public function update(ProductUpdateRequest $request, $id)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $user = $request->user();
            $data['image_url'] = $this->minio->uploadImage($request->file('image'), $user ? $user->id : 0);
        }

        $item = $this->repo->update((int) $id, $data);

        Log::info("Product updated: {$id}");

        return new ProductResource($item);
    }

    public function toggleActive(Request $request, $id)
    {
        $item = $this->repo->toggleActive((int) $id);

        return new ProductResource($item);
    }

    public function destroy($id)
    {
        $this->repo->delete((int) $id);

        Log::info("Product deleted: {$id}");

        return response()->json(null, 204);
    }
}
