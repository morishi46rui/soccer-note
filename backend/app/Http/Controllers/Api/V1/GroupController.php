<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Group\CreateGroupRequest;
use App\Http\Requests\Group\UpdateGroupRequest;
use App\Models\Group;
use App\UseCase\Group\CreateGroupAction;
use App\UseCase\Group\DeleteGroupAction;
use App\UseCase\Group\GetGroupAction;
use App\UseCase\Group\GetGroupsAction;
use App\UseCase\Group\UpdateGroupAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Groups', description: 'グループ管理')]
class GroupController extends Controller
{
    #[OA\Get(
        path: '/api/v1/groups',
        tags: ['Groups'],
        summary: 'グループ一覧取得',
        description: 'グループ一覧を取得します',
        operationId: 'getGroups',
        security: [['sanctum' => []]]
    )]
    #[OA\Parameter(ref: '#/components/parameters/page')]
    #[OA\Parameter(ref: '#/components/parameters/per_page')]
    #[OA\Response(
        response: '200',
        description: '取得成功',
        content: new OA\JsonContent(ref: '#/components/schemas/GetGroupsResponse')
    )]
    #[OA\Response(response: '401', ref: '#/components/responses/401')]
    public function index(Request $request, GetGroupsAction $action): JsonResponse
    {
        $page = (int) $request->get('page', 1);
        $perPage = (int) $request->get('per_page', 15);
        $groups = $action->execute($page, $perPage);

        return response()->json($groups, 200, [], JSON_UNESCAPED_UNICODE);
    }

    #[OA\Post(
        path: '/api/v1/groups',
        tags: ['Groups'],
        summary: 'グループ作成',
        description: '新しいグループを作成します',
        operationId: 'createGroup',
        security: [['sanctum' => []]]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(ref: '#/components/schemas/CreateGroupRequest')
    )]
    #[OA\Response(
        response: '201',
        description: '作成成功',
        content: new OA\JsonContent(ref: '#/components/schemas/CreateGroupResponse')
    )]
    #[OA\Response(response: '401', ref: '#/components/responses/401')]
    #[OA\Response(response: '422', ref: '#/components/responses/422')]
    public function store(CreateGroupRequest $request, CreateGroupAction $action): JsonResponse
    {
        $group = $action->execute($request->validated());

        return response()->json($group, 201, [], JSON_UNESCAPED_UNICODE);
    }

    #[OA\Get(
        path: '/api/v1/groups/{sqid}',
        tags: ['Groups'],
        summary: 'グループ詳細取得',
        description: '指定されたSqidのグループ詳細を取得します',
        operationId: 'getGroup',
        security: [['sanctum' => []]]
    )]
    #[OA\Parameter(
        name: 'sqid',
        in: 'path',
        required: true,
        description: 'グループSqid',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Response(
        response: '200',
        description: '取得成功',
        content: new OA\JsonContent(ref: '#/components/schemas/GetGroupResponse')
    )]
    #[OA\Response(response: '401', ref: '#/components/responses/401')]
    #[OA\Response(response: '404', ref: '#/components/responses/404')]
    public function show(string $sqid, GetGroupAction $action): JsonResponse
    {
        $groupId = Group::findBySqid($sqid)?->id;

        if ($groupId === null) {
            return response()->json([
                'message' => 'グループが見つかりませんでした',
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $group = $action->execute($groupId);

        if ($group === null) {
            return response()->json([
                'message' => 'グループが見つかりません',
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        return response()->json($group, 200, [], JSON_UNESCAPED_UNICODE);
    }

    #[OA\Put(
        path: '/api/v1/groups/{sqid}',
        tags: ['Groups'],
        summary: 'グループ更新',
        description: '指定されたSqidのグループを更新します',
        operationId: 'updateGroup',
        security: [['sanctum' => []]]
    )]
    #[OA\Parameter(
        name: 'sqid',
        in: 'path',
        required: true,
        description: 'グループSqid',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(ref: '#/components/schemas/UpdateGroupRequest')
    )]
    #[OA\Response(
        response: '200',
        description: '更新成功',
        content: new OA\JsonContent(ref: '#/components/schemas/UpdateGroupResponse')
    )]
    #[OA\Response(response: '401', ref: '#/components/responses/401')]
    #[OA\Response(response: '404', ref: '#/components/responses/404')]
    #[OA\Response(response: '422', ref: '#/components/responses/422')]
    public function update(string $sqid, UpdateGroupRequest $request, UpdateGroupAction $action, GetGroupAction $getGroupAction): JsonResponse
    {
        $groupId = Group::findBySqid($sqid)?->id;

        if ($groupId === null) {
            return response()->json([
                'message' => 'グループが見つかりませんでした',
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $group = $getGroupAction->execute($groupId);

        if ($group === null) {
            return response()->json([
                'message' => 'グループが見つかりません',
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $updatedGroup = $action->execute($group, $request->validated());

        return response()->json($updatedGroup, 200, [], JSON_UNESCAPED_UNICODE);
    }

    #[OA\Delete(
        path: '/api/v1/groups/{sqid}',
        tags: ['Groups'],
        summary: 'グループ削除',
        description: '指定されたSqidのグループを削除します',
        operationId: 'deleteGroup',
        security: [['sanctum' => []]]
    )]
    #[OA\Parameter(
        name: 'sqid',
        in: 'path',
        required: true,
        description: 'グループSqid',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Response(
        response: '204',
        description: '削除成功'
    )]
    #[OA\Response(response: '401', ref: '#/components/responses/401')]
    #[OA\Response(response: '404', ref: '#/components/responses/404')]
    public function destroy(string $sqid, DeleteGroupAction $action, GetGroupAction $getGroupAction): JsonResponse
    {
        $groupId = Group::findBySqid($sqid)?->id;

        if ($groupId === null) {
            return response()->json([
                'message' => 'グループが見つかりませんでした',
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $group = $getGroupAction->execute($groupId);

        if ($group === null) {
            return response()->json([
                'message' => 'グループが見つかりません',
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $action->execute($group);

        return response()->json(null, 204);
    }
}
