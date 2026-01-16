<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\UserSettingRepository;
use App\Models\UserSetting;
use OpenApi\Attributes as OA;

#[OA\Tag(name: "User Settings", description: "API Endpoints for User Settings")]
class UserSettingController extends Controller
{
    public function __construct(protected UserSettingRepository $repo)
    {}

    #[OA\Get(
        path: "/api/v1/settings",
        tags: ["User Settings"],
        summary: "Get all settings for the authenticated user",
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 200, description: "Successful operation")
        ]
    )]
    public function index(Request $request)
    {
        $userId = $request->user() ? $request->user()->id : 0;
        $settings = $this->repo->getAllForUser($userId);
        return response()->json(['data' => $settings]);
    }

    #[OA\Post(
        path: "/api/v1/settings",
        tags: ["User Settings"],
        summary: "Update or create a user setting",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["key", "value"],
                properties: [
                    new OA\Property(property: "key", type: "string"),
                    new OA\Property(property: "value", type: "string")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Setting updated")
        ]
    )]
    public function update(Request $request)
    {
        $data = $request->validate([
            'key' => 'required|string|max:255',
            'value' => 'nullable|string',
        ]);

        $userId = $request->user() ? $request->user()->id : 0;
        $setting = $this->repo->set($userId, $data['key'], $data['value']);
        
        return response()->json(['data' => $setting]);
    }
}
