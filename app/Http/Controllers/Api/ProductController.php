<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Repositories\ProductRepository;
use App\Traits\PaginationTrait;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Products', description: 'API Endpoints for Products')]
class ProductController extends Controller
{
    use PaginationTrait;

    public function __construct(
        protected ProductRepository $repo,
        protected \App\Services\MinioService $minio,
        protected \App\Services\AuditService $audit
    ) {}

    #[OA\Get(
        path: '/api/v1/products',
        tags: ['Products'],
        summary: 'List all products with pagination',
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
            $items = $this->repo->search($search, ['name', 'sku', 'description'], $perPage, ['category', 'subcategory']);
        } else {
            $items = $this->repo->paginate($perPage, ['*'], ['category', 'subcategory']);
        }

        return response()->json([
            'data' => $items->getCollection()->transform(function($item) {
                return $this->formatProduct($item);
            }),
            'meta' => $this->formatPagination($items)
        ]);
    }

    #[OA\Get(
        path: '/api/v1/products/dropdown',
        tags: ['Products'],
        summary: 'Get products for dropdown',
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Successful operation'),
        ]
    )]
    public function dropdown()
    {
        $items = $this->repo->all(['id', 'name', 'sku']);

        return response()->json([
            'data' => $items->map(function($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'sku' => $item->sku
                ];
            })
        ]);
    }

    #[OA\Post(
        path: '/api/v1/products',
        tags: ['Products'],
        summary: 'Create a new product',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    required: ['name', 'sku', 'price', 'stock'],
                    properties: [
                        new OA\Property(property: 'name', type: 'string'),
                        new OA\Property(property: 'sku', type: 'string'),
                        new OA\Property(property: 'price', type: 'number'),
                        new OA\Property(property: 'stock', type: 'integer'),
                        new OA\Property(property: 'image', type: 'string', format: 'binary'),
                        new OA\Property(property: 'category_id', type: 'integer'),
                        new OA\Property(property: 'subcategory_id', type: 'integer'),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Product created'),
        ]
    )]
    public function store(ProductStoreRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $user = $request->user();
            $url = $this->minio->uploadImage($request->file('image'), $user ? $user->id : 0);
            $data['image_url'] = $url;
        }

        $item = $this->repo->create($data);

        \Illuminate\Support\Facades\Log::info("Product created: {$item->id}");
        $this->audit->log('product.created', "Created product '{$item->name}' (ID: {$item->id})", $item);

        return response()->json(['data' => $this->formatProduct($item)], 201);
    }

    #[OA\Get(
        path: '/api/v1/products/{id}',
        tags: ['Products'],
        summary: 'Get product details',
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

        return response()->json(['data' => $this->formatProduct($item)]);
    }

    #[OA\Post(
        path: '/api/v1/products/{id}',
        tags: ['Products'],
        summary: 'Update product (Use POST with _method=PUT for multipart)',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(property: '_method', type: 'string', default: 'PUT'),
                        new OA\Property(property: 'name', type: 'string'),
                        new OA\Property(property: 'image', type: 'string', format: 'binary'),
                        new OA\Property(property: 'price', type: 'number'),
                        new OA\Property(property: 'stock', type: 'integer'),
                        new OA\Property(property: 'category_id', type: 'integer'),
                        new OA\Property(property: 'subcategory_id', type: 'integer'),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Product updated'),
        ]
    )]
    public function update(ProductUpdateRequest $request, $id)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $user = $request->user();
            $url = $this->minio->uploadImage($request->file('image'), $user ? $user->id : 0);
            $data['image_url'] = $url;
        }

        $item = $this->repo->update((int) $id, $data);

        \Illuminate\Support\Facades\Log::info("Product updated: {$id}");
        $this->audit->log('product.updated', "Updated product '{$item->name}' (ID: {$item->id})", $item);

        return response()->json(['data' => $this->formatProduct($item)]);
    }

    #[OA\Post(
        path: '/api/v1/products/{id}/toggle-active',
        tags: ['Products'],
        summary: 'Toggle product active status',
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

        return response()->json(['data' => $this->formatProduct($item)]);
    }

    private function formatProduct($item)
    {
        return [
            'id' => $item->id,
            'category_id' => $item->category_id,
            'subcategory_id' => $item->subcategory_id,
            'name' => $item->name,
            'sku' => $item->sku,
            'description' => $item->description,
            'image_url' => $item->image_url ? \Illuminate\Support\Facades\Storage::disk('minio_private')->temporaryUrl($item->image_url, now()->addMinutes(60)) : null,
            'price' => (float) $item->price,
            'quantity' => (int) $item->quantity,
            'active' => (bool) $item->active,
            'created_at' => $item->created_at?->toDateTimeString(),
            'updated_at' => $item->updated_at?->toDateTimeString(),
        ];
    }

    #[OA\Delete(
        path: '/api/v1/products/{id}',
        tags: ['Products'],
        summary: 'Delete a product',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Product deleted'),
        ]
    )]
    public function destroy($id)
    {
        $this->repo->delete((int) $id);

        \Illuminate\Support\Facades\Log::info("Product deleted: {$id}");
        $this->audit->log('product.deleted', "Deleted product ID: {$id}");

        return response()->json(null, 204);
    }
}
