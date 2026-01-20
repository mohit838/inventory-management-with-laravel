<?php
/**
 * @OA\Schema(
 *     schema="ProductResource",
 *     type="object",
 *     properties={
 *         @OA\Property(property="id", type="integer"),
 *         @OA\Property(property="name", type="string"),
 *         @OA\Property(property="sku", type="string"),
 *         @OA\Property(property="price", type="number"),
 *         @OA\Property(property="quantity", type="integer"),
 *         @OA\Property(property="image_url", type="string", nullable=true)
 *     }
 * )
 */
namespace App\Http\Controllers\Api\Docs;

use OpenApi\Attributes as OA;

class ProductDoc
{
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
    public function index() {}

    #[OA\Get(
        path: '/api/v1/products/dropdown',
        tags: ['Products'],
        summary: 'Get products for dropdown',
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Successful operation'),
        ]
    )]
    public function dropdown() {}

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
    public function store() {}

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
    public function show() {}

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
    public function update() {}

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
    public function toggleActive() {}

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
    public function destroy() {}
}
