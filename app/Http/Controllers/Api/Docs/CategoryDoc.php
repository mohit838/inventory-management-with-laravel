<?php

namespace App\Http\Controllers\Api\Docs;

use OpenApi\Attributes as OA;

class CategoryDoc
{
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
    public function index() {}

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
    public function store() {}

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
    public function show() {}

    #[OA\Get(
        path: '/api/v1/categories/dropdown',
        tags: ['Categories'],
        summary: 'Get categories for dropdown',
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Successful operation'),
        ]
    )]
    public function dropdown() {}

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
    public function update() {}

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
    public function toggleActive() {}

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
    public function destroy() {}
}
