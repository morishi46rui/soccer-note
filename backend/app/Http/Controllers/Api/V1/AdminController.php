<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\UseCase\Admin\AdminStatsAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Admin', description: '管理者機能')]
class AdminController extends Controller
{
    #[OA\Get(
        path: '/api/v1/admin/stats',
        tags: ['Admin'],
        summary: 'システム統計情報の取得',
        description: 'システム全体の統計情報を取得します',
        operationId: 'getAdminStats',
        security: [['sanctum' => []]]
    )]
    #[OA\Response(
        response: '200',
        description: '統計情報の取得成功',
        content: new OA\JsonContent(ref: '#/components/schemas/AdminStatsResponse')
    )]
    #[OA\Response(response: '401', ref: '#/components/responses/401')]
    public function getStats(Request $request, AdminStatsAction $action): JsonResponse
    {
        $response = $action->execute();

        return response()->json($response, 200, [], JSON_UNESCAPED_UNICODE);
    }
}
