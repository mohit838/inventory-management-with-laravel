<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MinioService;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Uploads', description: 'API Endpoints for File Uploads')]
class PublicUploadController extends Controller
{
    public function __construct(protected MinioService $minio) {}

    #[OA\Post(
        path: '/api/v1/uploads/public',
        tags: ['Uploads'],
        summary: 'Upload a public file to MinIO',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    required: ['image'],
                    properties: [
                        new OA\Property(property: 'image', type: 'string', format: 'binary'),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'File uploaded successfully'),
        ]
    )]
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240', // Max 10MB
        ]);

        $url = $this->minio->uploadPublic($request->file('image'));

        return response()->json([
            'data' => [
                'url' => $url
            ]
        ], 201);
    }
}
