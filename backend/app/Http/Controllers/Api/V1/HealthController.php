<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Health', description: 'ヘルスチェック')]
class HealthController extends Controller
{
    #[OA\Get(
        path: '/api/v1/health',
        tags: ['Health'],
        summary: 'ヘルスチェック',
        description: 'APIサーバーの稼働状態を確認します',
        operationId: 'getHealth'
    )]
    #[OA\Response(
        response: '200',
        description: '正常',
        content: new OA\JsonContent(
            required: ['status', 'timestamp'],
            properties: [
                new OA\Property(
                    property: 'status',
                    description: 'ステータス',
                    type: 'string',
                    example: 'ok'
                ),
                new OA\Property(
                    property: 'timestamp',
                    description: 'タイムスタンプ',
                    type: 'string',
                    example: '2025-01-10T12:00:00Z'
                ),
            ]
        )
    )]
    public function index(): JsonResponse
    {
        return response()->json([
            'status' => 'ok',
            'timestamp' => now()->toIso8601String(),
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
}
