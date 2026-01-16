<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\MinioService;

class PublicUploadController extends Controller
{
    public function __construct(protected MinioService $minio)
    {}

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
