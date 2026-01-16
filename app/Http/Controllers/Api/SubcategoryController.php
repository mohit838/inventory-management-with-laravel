<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\SubcategoryRepository;
use App\Models\Subcategory;

class SubcategoryController extends Controller
{
    public function __construct(protected SubcategoryRepository $repo)
    {}

    public function index(Request $request)
    {
        $items = $this->repo->all(['*'], ['category']);
        return response()->json(\App\Http\Resources\SubcategoryResource::collection($items));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:subcategories,slug',
            'active' => 'sometimes|boolean',
        ]);
        
        $dto = \App\DTO\SubcategoryData::fromArray($data);
        $item = $this->repo->create($dto->toArray());
        return response()->json(new \App\Http\Resources\SubcategoryResource($item), 201);
    }

    public function show($id)
    {
        $item = $this->repo->find((int)$id, ['category']);
        return response()->json(new \App\Http\Resources\SubcategoryResource($item));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:subcategories,slug,' . $id,
            'active' => 'sometimes|boolean',
        ]);

        $dto = \App\DTO\SubcategoryData::fromArray($data);
        $item = $this->repo->update((int)$id, $dto->toArray());
        return response()->json(new \App\Http\Resources\SubcategoryResource($item));
    }

    public function toggleActive(Request $request, $id)
    {
        $item = $this->repo->toggleActive((int)$id);
        return response()->json(new \App\Http\Resources\SubcategoryResource($item));
    }

    public function destroy($id)
    {
        $this->repo->delete((int)$id);
        return response()->json(null, 204);
    }
}
