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
        
        return CategoryResource::collection($items);
    }

    public function dropdown()
    {
        $items = $this->repo->all(['id', 'name']);
        return response()->json($items); // Dropdown remains simple JSON array
    }

    public function store(CategoryStoreRequest $request)
    {
        $dto = \App\DTO\CategoryData::fromArray($request->validated());
        $item = $this->repo->create($dto->toArray());
        return (new CategoryResource($item))->response()->setStatusCode(201);
    }

    public function show($id)
    {
        $item = $this->repo->findWithInactive($id);
        return new CategoryResource($item);
    }

    public function update(CategoryUpdateRequest $request, $id)
    {
        $dto = \App\DTO\CategoryData::fromArray($request->validated());
        $item = $this->repo->update((int)$id, $dto->toArray());
        return new CategoryResource($item);
    }

    public function toggleActive(Request $request, $id)
    {
        $item = $this->repo->toggleActive((int)$id);
        return new CategoryResource($item);
    }

    public function destroy($id)
    {
        $this->repo->delete((int)$id);
        return response()->json(null, 204);
    }
}
