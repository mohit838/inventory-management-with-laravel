<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: 'Inventory Management API',
    version: '1.0.0',
    description: 'API documentation for Inventory Management System'
)]
#[OA\Server(
    url: \L5_SWAGGER_CONST_HOST,
    description: 'API Server'
)]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'JWT'
)]
abstract class Controller
{
    //
}
