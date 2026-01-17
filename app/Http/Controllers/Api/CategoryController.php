<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\CategoryRepository;
use App\Traits\PaginationTrait;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Categories', description: 'API Endpoints for Product Categories')]
class CategoryController extends Controller
{
    use PaginationTrait;

    public function __construct(protected CategoryRepository $repo) {}

    #[OA\Get(
        path: '/api/v1/categories',
        tags: ['Categories'],
        summary: 'List all categories with pagination',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'per_page', in: 'query', schema: new OA\Schema(type: 'integer', default: 15)),
            new OA\Parameter(name: 'search', in: 'query', schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Successful operation'),
        ]
    )]
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $search = $request->input('search');

        if ($search) {
            $items = $this->repo->search($search, ['name', 'slug'], $perPage, ['subcategories']);
        } else {
            $items = $this->repo->paginate($perPage, ['*'], ['subcategories']);
        }

        return response()->json([
            'data' => $items->getCollection()->transform(function($item) {
                return $this->formatCategory($item);
            }),
            'meta' => $this->formatPagination($items)
        ]);
    }

    #[OA\Post(
        path: '/api/v1/categories',
        tags: ['Categories'],
        summary: 'Create a new category',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'slug'],
                properties: [
                    new OA\Property(property: 'name', type: 'string'),
                    new OA\Property(property: 'slug', type: 'string'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Category created'),
        ]
    )]
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:categories,slug',
            'description' => 'nullable|string',
            'active' => 'sometimes|boolean',
        ]);

        $item = $this->repo->create($data);

        return response()->json(['data' => $this->formatCategory($item)], 201);
    }

    #[OA\Get(
        path: '/api/v1/categories/{id}',
        tags: ['Categories'],
        summary: 'Get category details',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Successful operation'),
        ]
    )]
    public function show($id)
    {
        $item = $this->repo->findWithInactive($id);

        return response()->json(['data' => $this->formatCategory($item)]);
    }

    #[OA\Get(
        path: '/api/v1/categories/dropdown',
        tags: ['Categories'],
        summary: 'Get categories for dropdown',
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Successful operation'),
        ]
    )]
    public function dropdown()
    {
        $items = $this->repo->all(['id', 'name']);
        
        return response()->json([
            'data' => $items->map(function($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name
                ];
            })
        ]);
    }

    #[OA\Put(
        path: '/api/v1/categories/{id}',
        tags: ['Categories'],
        summary: 'Update a category',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string'),
                    new OA\Property(property: 'slug', type: 'string'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Category updated'),
        ]
    )]
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|max:255|unique:categories,slug,' . $id,
            'description' => 'nullable|string',
            'active' => 'sometimes|boolean',
        ]);

        $item = $this->repo->update((int) $id, $data);

        return response()->json(['data' => $this->formatCategory($item)]);
    }

    private function formatCategory($item)
    {
        return [
            'id' => $item->id,
            'name' => $item->name,
            'slug' => $item->slug,
            'description' => $item->description,
            'active' => (bool) $item->active,
            'created_at' => $item->created_at?->toDateTimeString(),
            'updated_at' => $item->updated_at?->toDateTimeString(),
        ];
    }

    #[OA\Post(
        path: '/api/v1/categories/{id}/toggle-active',
        tags: ['Categories'],
        summary: 'Toggle category active status',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Successful operation'),
        ]
    )]
    public function toggleActive(Request $request, $id)
    {
        $item = $this->repo->toggleActive((int) $id);

        return response()->json(['data' => $this->formatCategory($item)]);
    }

    #[OA\Delete(
        path: '/api/v1/categories/{id}',
        tags: ['Categories'],
        summary: 'Delete a category',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Category deleted'),
        ]
    )]
    public function destroy($id)
    {
        $this->repo->delete((int) $id);

        return response()->json(null, 204);
    }
}
