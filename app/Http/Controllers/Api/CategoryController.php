<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\CategoryRepository;
use App\Models\Category;
use App\Http\Requests\CategoryStoreRequest;
use App\Http\Requests\CategoryUpdateRequest;
use App\Http\Resources\CategoryResource;

class CategoryController extends Controller
{
    public function __construct(protected CategoryRepository $repo)
    {}

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', \App\Constants\GlobalConstants::PER_PAGE_DEFAULT);
        $search = $request->input('search');

        if ($search) {
            $items = $this->repo->search($search, ['name', 'slug'], $perPage, ['subcategories']);
        } else {
            $items = $this->repo->paginate($perPage, ['*'], ['subcategories']);
        }
        
        return response()->json(CategoryResource::collection($items));
    }

    public function dropdown()
    {
        // Dropdowns usually return lightweight list. Cache this heavily.
        // We can add a specialized method in repo or just use all() and map here.
        // For simplicity and to use repo caching, let's use all() but we might want to ensure it uses a cache tag or key.
        // Repo->all() is cached.
        $items = $this->repo->all(['id', 'name']);
        return response()->json($items);
    }

    public function store(CategoryStoreRequest $request)
    {
        $dto = \App\DTO\CategoryData::fromArray($request->validated());
        $item = $this->repo->create($dto->toArray());
        return response()->json(new CategoryResource($item), 201);
    }

    public function show($id)
    {
        $item = $this->repo->find($id, ['subcategories']);
        return response()->json(new CategoryResource($item));
    }

    public function update(CategoryUpdateRequest $request, $id)
    {
        $dto = \App\DTO\CategoryData::fromArray($request->validated());
        $item = $this->repo->update((int)$id, $dto->toArray());
        return response()->json(new CategoryResource($item));
    }

    public function toggleActive(Request $request, $id)
    {
        $item = $this->repo->toggleActive((int)$id);
        return response()->json(new CategoryResource($item));
    }

    public function destroy($id)
    {
        $this->repo->delete((int)$id);
        return response()->json(null, 204);
    }
}
