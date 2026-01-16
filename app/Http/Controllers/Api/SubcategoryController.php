<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\SubcategoryRepository;
use App\Http\Resources\SubcategoryResource;
use App\Http\Requests\SubcategoryStoreRequest;
use App\Http\Requests\SubcategoryUpdateRequest;
use App\Traits\PaginationTrait;
use OpenApi\Attributes as OA;

#[OA\Tag(name: "Subcategories", description: "API Endpoints for Product Subcategories")]
class SubcategoryController extends Controller
{
    use PaginationTrait;

    public function __construct(protected SubcategoryRepository $repo)
    {}

    #[OA\Get(
        path: "/api/v1/subcategories",
        tags: ["Subcategories"],
        summary: "List all subcategories with pagination",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "per_page", in: "query", schema: new OA\Schema(type: "integer", default: 15)),
            new OA\Parameter(name: "search", in: "query", schema: new OA\Schema(type: "string"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Successful operation")
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
        
        return SubcategoryResource::collection($items)
            ->additional(['meta' => $this->formatPagination($items)]);
    }

    #[OA\Post(
        path: "/api/v1/subcategories",
        tags: ["Subcategories"],
        summary: "Create a new subcategory",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["category_id", "name", "slug"],
                properties: [
                    new OA\Property(property: "category_id", type: "integer"),
                    new OA\Property(property: "name", type: "string"),
                    new OA\Property(property: "slug", type: "string"),
                    new OA\Property(property: "active", type: "boolean")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Subcategory created")
        ]
    )]
    public function store(SubcategoryStoreRequest $request)
    {
        $item = $this->repo->create($request->validated());
        return (new SubcategoryResource($item))->response()->setStatusCode(201);
    }

    #[OA\Get(
        path: "/api/v1/subcategories/{id}",
        tags: ["Subcategories"],
        summary: "Get subcategory details",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Successful operation")
        ]
    )]
    public function show($id)
    {
        $item = $this->repo->findWithInactive((int)$id);
        return new SubcategoryResource($item);
    }

    #[OA\Get(
        path: "/api/v1/subcategories/dropdown",
        tags: ["Subcategories"],
        summary: "Get subcategories for dropdown",
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 200, description: "Successful operation")
        ]
    )]
    public function dropdown()
    {
        return SubcategoryResource::collection($this->repo->all());
    }

    #[OA\Put(
        path: "/api/v1/subcategories/{id}",
        tags: ["Subcategories"],
        summary: "Update a subcategory",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "category_id", type: "integer"),
                    new OA\Property(property: "name", type: "string"),
                    new OA\Property(property: "slug", type: "string"),
                    new OA\Property(property: "active", type: "boolean")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Subcategory updated")
        ]
    )]
    public function update(SubcategoryUpdateRequest $request, $id)
    {
        $item = $this->repo->update((int)$id, $request->validated());
        return new SubcategoryResource($item);
    }

    #[OA\Post(
        path: "/api/v1/subcategories/{id}/toggle-active",
        tags: ["Subcategories"],
        summary: "Toggle subcategory active status",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Successful operation")
        ]
    )]
    public function toggleActive(Request $request, $id)
    {
        $item = $this->repo->toggleActive((int)$id);
        return new SubcategoryResource($item);
    }

    #[OA\Delete(
        path: "/api/v1/subcategories/{id}",
        tags: ["Subcategories"],
        summary: "Delete a subcategory",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 204, description: "Subcategory deleted")
        ]
    )]
    public function destroy($id)
    {
        $this->repo->delete((int)$id);
        return response()->json(null, 204);
    }
}
