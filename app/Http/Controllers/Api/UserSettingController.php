<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\UserSettingRepository;
use App\Http\Requests\UserSettingUpdateRequest;
use App\Http\Resources\UserSettingResource;
use Illuminate\Http\Request;
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
        return UserSettingResource::collection($settings);
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
    public function update(UserSettingUpdateRequest $request)
    {
        $userId = $request->user() ? $request->user()->id : 0;
        $setting = $this->repo->set($userId, $request->key, $request->value);
        
        return (new UserSettingResource($setting))->response()->setStatusCode(200);
    }
}
