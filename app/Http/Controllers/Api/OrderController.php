<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderStoreRequest;
use App\Http\Resources\OrderResource;
use App\Services\OrderService;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Orders', description: 'API Endpoints for Orders')]
class OrderController extends Controller
{
    public function __construct(protected OrderService $service) {}

    #[OA\Post(
        path: '/api/v1/orders',
        tags: ['Orders'],
        summary: 'Create a new order',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['customer_name', 'payment_method', 'items'],
                properties: [
                    new OA\Property(property: 'customer_name', type: 'string'),
                    new OA\Property(property: 'customer_email', type: 'string', format: 'email'),
                    new OA\Property(property: 'payment_method', type: 'string', enum: ['cash', 'card', 'online']),
                    new OA\Property(
                        property: 'items',
                        type: 'array',
                        items: new OA\Items(
                            properties: [
                                new OA\Property(property: 'product_id', type: 'integer'),
                                new OA\Property(property: 'quantity', type: 'integer'),
                            ]
                        )
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Order created'),
            new OA\Response(response: 400, description: 'Bad Request (e.g., out of stock)'),
        ]
    )]
    public function store(OrderStoreRequest $request)
    {
        try {
            $order = $this->service->createOrder($request->validated(), $request->user() ? $request->user()->id : 0);

            return (new OrderResource($order))->response()->setStatusCode(201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    #[OA\Get(
        path: '/api/v1/orders/{id}/invoice',
        tags: ['Orders'],
        summary: 'Generate invoice for an order',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Successful operation'),
        ]
    )]
    public function invoice(int $id)
    {
        try {
            $invoice = $this->service->generateInvoice($id);

            return response()->json(['data' => $invoice]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Invoice generation failed'], 500);
        }
    }
}
