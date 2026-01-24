<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Interfaces\CategoryRepositoryInterface;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct(protected CategoryRepositoryInterface $repo) {}

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $search = $request->input('search');

        if ($search) {
            $items = $this->repo->search($search, ['name', 'slug'], $perPage, ['subcategories']);
        } else {
            $items = $this->repo->paginate($perPage, ['*'], ['subcategories']);
        }

        return CategoryResource::collection($items);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:categories,slug',
            'description' => 'nullable|string',
            'active' => 'sometimes|boolean',
        ]);

        $item = $this->repo->create($data);

        return new CategoryResource($item);
    }

    public function show($id)
    {
        $item = $this->repo->findWithInactive($id);

        return new CategoryResource($item);
    }

    public function dropdown()
    {
        $items = $this->repo->all(['id', 'name']);

        return response()->json([
            'data' => $items->map(fn($item) => [
                'id' => $item->id,
                'name' => $item->name
            ])
        ]);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|max:255|unique:categories,slug,' . $id,
            'description' => 'nullable|string',
            'active' => 'sometimes|boolean',
        ]);

        $item = $this->repo->update((int) $id, $data);

        return new CategoryResource($item);
    }

    public function toggleActive(Request $request, $id)
    {
        $item = $this->repo->toggleActive((int) $id);

        return new CategoryResource($item);
    }

    public function destroy($id)
    {
        $this->repo->delete((int) $id);

        return response()->json(null, 204);
    }
}
