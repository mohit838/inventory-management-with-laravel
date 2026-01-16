<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\NotificationResource;
use OpenApi\Attributes as OA;

#[OA\Tag(name: "Notifications", description: "API Endpoints for User Notifications")]
class NotificationController extends Controller
{
    #[OA\Get(
        path: "/api/v1/notifications",
        tags: ["Notifications"],
        summary: "List all notifications for the authenticated user",
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 200, description: "Successful operation")
        ]
    )]
    public function index(Request $request)
    {
        $notifications = $request->user()->notifications()->paginate(20);
        return NotificationResource::collection($notifications);
    }

    #[OA\Patch(
        path: "/api/v1/notifications/{id}/read",
        tags: ["Notifications"],
        summary: "Mark a notification as read",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "string"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Successful operation")
        ]
    )]
    public function markAsRead(Request $request, $id)
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        
        return new NotificationResource($notification);
    }

    #[OA\Patch(
        path: "/api/v1/notifications/read-all",
        tags: ["Notifications"],
        summary: "Mark all notifications as read",
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 200, description: "Successful operation")
        ]
    )]
    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();
        return response()->json(['message' => 'All notifications marked as read']);
    }
}
