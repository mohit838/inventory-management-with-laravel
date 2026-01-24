<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SubcategoryResource;
use App\Interfaces\SubcategoryRepositoryInterface;
use Illuminate\Http\Request;

class SubcategoryController extends Controller
{
    public function __construct(protected SubcategoryRepositoryInterface $repo) {}

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $search = $request->input('search');

        if ($search) {
            $items = $this->repo->search($search, ['name', 'slug'], $perPage, ['category']);
        } else {
            $items = $this->repo->paginate($perPage, ['*'], ['category']);
        }

        return SubcategoryResource::collection($items);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:subcategories,slug',
            'active' => 'sometimes|boolean',
        ]);

        $item = $this->repo->create($data);

        return new SubcategoryResource($item);
    }

    public function show($id)
    {
        $item = $this->repo->findWithInactive((int) $id);

        return new SubcategoryResource($item);
    }

    public function dropdown()
    {
        $items = $this->repo->all();
        
        return response()->json([
            'data' => $items->map(fn($item) => [
                'id' => $item->id,
                'name' => $item->name,
                'category_id' => $item->category_id
            ])
        ]);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'category_id' => 'sometimes|exists:categories,id',
            'name' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|max:255|unique:subcategories,slug,' . $id,
            'active' => 'sometimes|boolean',
        ]);

        $item = $this->repo->update((int) $id, $data);

        return new SubcategoryResource($item);
    }

    public function toggleActive(Request $request, $id)
    {
        $item = $this->repo->toggleActive((int) $id);

        return new SubcategoryResource($item);
    }

    public function destroy($id)
    {
        $this->repo->delete((int) $id);

        return response()->json(null, 204);
    }
}
