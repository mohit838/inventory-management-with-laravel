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
        $items = $this->repo->all(['*'], ['subcategories']);
        return response()->json(CategoryResource::collection($items));
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
