<?php

namespace App\Http\Controllers\Api\Docs;

use OpenApi\Attributes as OA;

class SubcategoryDoc
{
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
    public function index() {}

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
    public function store() {}

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
    public function show() {}

    #[OA\Get(
        path: '/api/v1/subcategories/dropdown',
        tags: ['Subcategories'],
        summary: 'Get subcategories for dropdown',
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Successful operation'),
        ]
    )]
    public function dropdown() {}

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
    public function update() {}

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
    public function toggleActive() {}

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
    public function destroy() {}
}
