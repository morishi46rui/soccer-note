<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\UseCase\Role\GetRolesAction;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Roles', description: 'ロール管理')]
class RoleController extends Controller
{
    #[OA\Get(
        path: '/api/v1/roles',
        tags: ['Roles'],
        summary: 'ロール一覧取得',
        description: 'すべてのロールと関連する権限を取得します',
        operationId: 'getRoles',
        security: [['sanctum' => []]]
    )]
    #[OA\Response(
        response: '200',
        description: '取得成功',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(
                properties: [
                    new OA\Property(property: 'id', type: 'integer', format: 'int64', description: 'ロールID', example: 1),
                    new OA\Property(property: 'name', type: 'string', description: 'ロール識別子', example: 'player'),
                    new OA\Property(property: 'display_name', type: 'string', description: '表示名', example: '選手'),
                    new OA\Property(property: 'description', type: 'string', nullable: true, description: 'ロールの説明', example: 'チームの選手'),
                    new OA\Property(
                        property: 'permissions',
                        type: 'array',
                        description: 'このロールが持つ権限',
                        items: new OA\Items(
                            properties: [
                                new OA\Property(property: 'id', type: 'integer', format: 'int64', description: '権限ID', example: 1),
                                new OA\Property(property: 'name', type: 'string', description: '権限識別子', example: 'view_notes'),
                                new OA\Property(property: 'display_name', type: 'string', description: '表示名', example: 'ノート閲覧'),
                                new OA\Property(property: 'description', type: 'string', nullable: true, description: '権限の説明', example: 'ノートを閲覧できる'),
                            ]
                        )
                    ),
                    new OA\Property(property: 'created_at', type: 'string', format: 'date-time', description: '作成日時'),
                    new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', description: '更新日時'),
                ]
            )
        )
    )]
    #[OA\Response(response: '401', ref: '#/components/responses/401')]
    public function index(GetRolesAction $action): JsonResponse
    {
        $roles = $action->execute();

        return response()->json($roles, 200, [], JSON_UNESCAPED_UNICODE);
    }
}
