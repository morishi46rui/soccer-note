<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\UseCase\Permission\GetPermissionsAction;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Permissions', description: '権限管理')]
class PermissionController extends Controller
{
    #[OA\Get(
        path: '/api/v1/permissions',
        tags: ['Permissions'],
        summary: '権限一覧取得',
        description: 'すべての権限を取得します',
        operationId: 'getPermissions',
        security: [['sanctum' => []]]
    )]
    #[OA\Response(
        response: '200',
        description: '取得成功',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(
                properties: [
                    new OA\Property(property: 'id', type: 'integer', format: 'int64', description: '権限ID', example: 1),
                    new OA\Property(property: 'name', type: 'string', description: '権限識別子', example: 'view_notes'),
                    new OA\Property(property: 'display_name', type: 'string', description: '表示名', example: 'ノート閲覧'),
                    new OA\Property(property: 'description', type: 'string', nullable: true, description: '権限の説明', example: 'ノートを閲覧できる'),
                    new OA\Property(property: 'created_at', type: 'string', format: 'date-time', description: '作成日時'),
                    new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', description: '更新日時'),
                ]
            )
        )
    )]
    #[OA\Response(response: '401', ref: '#/components/responses/401')]
    public function index(GetPermissionsAction $action): JsonResponse
    {
        $permissions = $action->execute();

        return response()->json($permissions, 200, [], JSON_UNESCAPED_UNICODE);
    }
}
