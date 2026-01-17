<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\SubcategoryRepository;
use App\Traits\PaginationTrait;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Subcategories', description: 'API Endpoints for Product Subcategories')]
class SubcategoryController extends Controller
{
    use PaginationTrait;

    public function __construct(protected SubcategoryRepository $repo) {}

    #[OA\Get(
        path: '/api/v1/subcategories',
        tags: ['Subcategories'],
        summary: 'List all subcategories with pagination',
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
            $items = $this->repo->search($search, ['name', 'slug'], $perPage, ['category']);
        } else {
            $items = $this->repo->paginate($perPage, ['*'], ['category']);
        }

        return response()->json([
            'data' => $items->getCollection()->transform(function($item) {
                return $this->formatSubcategory($item);
            }),
            'meta' => $this->formatPagination($items)
        ]);
    }

    #[OA\Post(
        path: '/api/v1/subcategories',
        tags: ['Subcategories'],
        summary: 'Create a new subcategory',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['category_id', 'name', 'slug'],
                properties: [
                    new OA\Property(property: 'category_id', type: 'integer'),
                    new OA\Property(property: 'name', type: 'string'),
                    new OA\Property(property: 'slug', type: 'string'),
                    new OA\Property(property: 'active', type: 'boolean'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Subcategory created'),
        ]
    )]
    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:subcategories,slug',
            'active' => 'sometimes|boolean',
        ]);

        $item = $this->repo->create($data);

        return response()->json(['data' => $this->formatSubcategory($item)], 201);
    }

    #[OA\Get(
        path: '/api/v1/subcategories/{id}',
        tags: ['Subcategories'],
        summary: 'Get subcategory details',
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
        $item = $this->repo->findWithInactive((int) $id);

        return response()->json(['data' => $this->formatSubcategory($item)]);
    }

    #[OA\Get(
        path: '/api/v1/subcategories/dropdown',
        tags: ['Subcategories'],
        summary: 'Get subcategories for dropdown',
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Successful operation'),
        ]
    )]
    public function dropdown()
    {
        $items = $this->repo->all();
        
        return response()->json([
            'data' => $items->map(function($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'category_id' => $item->category_id
                ];
            })
        ]);
    }

    #[OA\Put(
        path: '/api/v1/subcategories/{id}',
        tags: ['Subcategories'],
        summary: 'Update a subcategory',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'category_id', type: 'integer'),
                    new OA\Property(property: 'name', type: 'string'),
                    new OA\Property(property: 'slug', type: 'string'),
                    new OA\Property(property: 'active', type: 'boolean'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Subcategory updated'),
        ]
    )]
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'category_id' => 'sometimes|exists:categories,id',
            'name' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|max:255|unique:subcategories,slug,' . $id,
            'active' => 'sometimes|boolean',
        ]);

        $item = $this->repo->update((int) $id, $data);

        return response()->json(['data' => $this->formatSubcategory($item)]);
    }

    private function formatSubcategory($item)
    {
        return [
            'id' => $item->id,
            'category_id' => $item->category_id,
            'name' => $item->name,
            'slug' => $item->slug,
            'active' => (bool)$item->active,
            'category' => $item->relationLoaded('category') ? [
                'id' => $item->category->id,
                'name' => $item->category->name
            ] : null,
            'created_at' => $item->created_at?->toDateTimeString(),
            'updated_at' => $item->updated_at?->toDateTimeString(),
        ];
    }

    #[OA\Post(
        path: '/api/v1/subcategories/{id}/toggle-active',
        tags: ['Subcategories'],
        summary: 'Toggle subcategory active status',
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

        return response()->json(['data' => $this->formatSubcategory($item)]);
    }

    #[OA\Delete(
        path: '/api/v1/subcategories/{id}',
        tags: ['Subcategories'],
        summary: 'Delete a subcategory',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Subcategory deleted'),
        ]
    )]
    public function destroy($id)
    {
        $this->repo->delete((int) $id);

        return response()->json(null, 204);
    }
}
