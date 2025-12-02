<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Team\AddUserToTeamRequest;
use App\Http\Requests\Team\CreateTeamRequest;
use App\Http\Requests\Team\UpdateTeamRequest;
use App\Http\Requests\Team\UpdateTeamUserRequest;
use App\Models\Team;
use App\UseCase\Team\AddUserToTeamAction;
use App\UseCase\Team\CreateTeamAction;
use App\UseCase\Team\DeleteTeamAction;
use App\UseCase\Team\GetTeamAction;
use App\UseCase\Team\GetTeamsAction;
use App\UseCase\Team\GetTeamUsersAction;
use App\UseCase\Team\RemoveUserFromTeamAction;
use App\UseCase\Team\UpdateTeamAction;
use App\UseCase\Team\UpdateTeamUserAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Teams', description: 'チーム管理')]
class TeamController extends Controller
{
    #[OA\Get(
        path: '/api/v1/teams',
        tags: ['Teams'],
        summary: 'チーム一覧取得',
        description: 'チーム一覧を取得します',
        operationId: 'getTeams',
        security: [['sanctum' => []]]
    )]
    #[OA\Parameter(ref: '#/components/parameters/page')]
    #[OA\Parameter(ref: '#/components/parameters/per_page')]
    #[OA\Response(
        response: '200',
        description: '取得成功',
        content: new OA\JsonContent(ref: '#/components/schemas/GetTeamsResponse')
    )]
    #[OA\Response(response: '401', ref: '#/components/responses/401')]
    public function index(Request $request, GetTeamsAction $action): JsonResponse
    {
        $page = (int) $request->get('page', 1);
        $perPage = (int) $request->get('per_page', 15);
        $teams = $action->execute($page, $perPage);

        return response()->json($teams, 200, [], JSON_UNESCAPED_UNICODE);
    }

    #[OA\Post(
        path: '/api/v1/teams',
        tags: ['Teams'],
        summary: 'チーム作成',
        description: '新しいチームを作成します',
        operationId: 'createTeam',
        security: [['sanctum' => []]]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(ref: '#/components/schemas/CreateTeamRequest')
    )]
    #[OA\Response(
        response: '201',
        description: '作成成功',
        content: new OA\JsonContent(ref: '#/components/schemas/CreateTeamResponse')
    )]
    #[OA\Response(response: '401', ref: '#/components/responses/401')]
    #[OA\Response(response: '422', ref: '#/components/responses/422')]
    public function store(CreateTeamRequest $request, CreateTeamAction $action): JsonResponse
    {
        $team = $action->execute($request->validated());

        return response()->json($team, 201, [], JSON_UNESCAPED_UNICODE);
    }

    #[OA\Get(
        path: '/api/v1/teams/{sqid}',
        tags: ['Teams'],
        summary: 'チーム詳細取得',
        description: '指定されたSqidのチーム詳細を取得します',
        operationId: 'getTeam',
        security: [['sanctum' => []]]
    )]
    #[OA\Parameter(
        name: 'sqid',
        in: 'path',
        required: true,
        description: 'チームSqid',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Response(
        response: '200',
        description: '取得成功',
        content: new OA\JsonContent(ref: '#/components/schemas/GetTeamResponse')
    )]
    #[OA\Response(response: '401', ref: '#/components/responses/401')]
    #[OA\Response(response: '404', ref: '#/components/responses/404')]
    public function show(string $sqid, GetTeamAction $action): JsonResponse
    {
        $teamId = Team::findBySqid($sqid)?->id;

        if ($teamId === null) {
            return response()->json([
                'message' => 'チームが見つかりませんでした',
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $team = $action->execute($teamId);

        if ($team === null) {
            return response()->json([
                'message' => 'チームが見つかりません',
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        return response()->json($team, 200, [], JSON_UNESCAPED_UNICODE);
    }

    #[OA\Put(
        path: '/api/v1/teams/{sqid}',
        tags: ['Teams'],
        summary: 'チーム更新',
        description: '指定されたSqidのチームを更新します',
        operationId: 'updateTeam',
        security: [['sanctum' => []]]
    )]
    #[OA\Parameter(
        name: 'sqid',
        in: 'path',
        required: true,
        description: 'チームSqid',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(ref: '#/components/schemas/UpdateTeamRequest')
    )]
    #[OA\Response(
        response: '200',
        description: '更新成功',
        content: new OA\JsonContent(ref: '#/components/schemas/UpdateTeamResponse')
    )]
    #[OA\Response(response: '401', ref: '#/components/responses/401')]
    #[OA\Response(response: '404', ref: '#/components/responses/404')]
    #[OA\Response(response: '422', ref: '#/components/responses/422')]
    public function update(string $sqid, UpdateTeamRequest $request, UpdateTeamAction $action, GetTeamAction $getTeamAction): JsonResponse
    {
        $teamId = Team::findBySqid($sqid)?->id;

        if ($teamId === null) {
            return response()->json([
                'message' => 'チームが見つかりませんでした',
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $team = $getTeamAction->execute($teamId);

        if ($team === null) {
            return response()->json([
                'message' => 'チームが見つかりません',
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $updatedTeam = $action->execute($team, $request->validated());

        return response()->json($updatedTeam, 200, [], JSON_UNESCAPED_UNICODE);
    }

    #[OA\Delete(
        path: '/api/v1/teams/{sqid}',
        tags: ['Teams'],
        summary: 'チーム削除',
        description: '指定されたSqidのチームを削除します',
        operationId: 'deleteTeam',
        security: [['sanctum' => []]]
    )]
    #[OA\Parameter(
        name: 'sqid',
        in: 'path',
        required: true,
        description: 'チームSqid',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Response(
        response: '204',
        description: '削除成功'
    )]
    #[OA\Response(response: '401', ref: '#/components/responses/401')]
    #[OA\Response(response: '404', ref: '#/components/responses/404')]
    public function destroy(string $sqid, DeleteTeamAction $action, GetTeamAction $getTeamAction): JsonResponse
    {
        $teamId = Team::findBySqid($sqid)?->id;

        if ($teamId === null) {
            return response()->json([
                'message' => 'チームが見つかりませんでした',
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $team = $getTeamAction->execute($teamId);

        if ($team === null) {
            return response()->json([
                'message' => 'チームが見つかりません',
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $action->execute($team);

        return response()->json(null, 204);
    }

    #[OA\Post(
        path: '/api/v1/teams/{sqid}/users',
        tags: ['Teams'],
        summary: 'チームにユーザーを追加',
        description: '指定されたSqidのチームにユーザーを追加します',
        operationId: 'addUserToTeam',
        security: [['sanctum' => []]]
    )]
    #[OA\Parameter(
        name: 'sqid',
        in: 'path',
        required: true,
        description: 'チームSqid',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(ref: '#/components/schemas/AddUserToTeamRequest')
    )]
    #[OA\Response(
        response: '201',
        description: '追加成功',
        content: new OA\JsonContent(ref: '#/components/schemas/AddUserToTeamResponse')
    )]
    #[OA\Response(response: '401', ref: '#/components/responses/401')]
    #[OA\Response(response: '404', ref: '#/components/responses/404')]
    #[OA\Response(response: '422', ref: '#/components/responses/422')]
    public function addUser(string $sqid, AddUserToTeamRequest $request, AddUserToTeamAction $action, GetTeamAction $getTeamAction): JsonResponse
    {
        $teamId = Team::findBySqid($sqid)?->id;

        if ($teamId === null) {
            return response()->json([
                'message' => 'チームが見つかりませんでした',
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $team = $getTeamAction->execute($teamId);

        if ($team === null) {
            return response()->json([
                'message' => 'チームが見つかりません',
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $teamUser = $action->execute(
            $team,
            $request->validated()['email'],
            $request->validated()['is_owner'] ?? false
        );

        if ($teamUser === null) {
            return response()->json([
                'message' => 'ユーザーが見つからないか、すでにチームに登録されています',
            ], 422, [], JSON_UNESCAPED_UNICODE);
        }

        return response()->json($teamUser, 201, [], JSON_UNESCAPED_UNICODE);
    }

    #[OA\Get(
        path: '/api/v1/teams/{sqid}/users',
        tags: ['Teams'],
        summary: 'チームのユーザー一覧取得',
        description: '指定されたSqidのチームに所属するユーザー一覧を取得します',
        operationId: 'getTeamUsers',
        security: [['sanctum' => []]]
    )]
    #[OA\Parameter(
        name: 'sqid',
        in: 'path',
        required: true,
        description: 'チームSqid',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Response(
        response: '200',
        description: '取得成功',
        content: new OA\JsonContent(ref: '#/components/schemas/GetTeamUsersResponse')
    )]
    #[OA\Response(response: '401', ref: '#/components/responses/401')]
    #[OA\Response(response: '404', ref: '#/components/responses/404')]
    public function getUsers(string $sqid, GetTeamUsersAction $action, GetTeamAction $getTeamAction): JsonResponse
    {
        $teamId = Team::findBySqid($sqid)?->id;

        if ($teamId === null) {
            return response()->json([
                'message' => 'チームが見つかりませんでした',
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $team = $getTeamAction->execute($teamId);

        if ($team === null) {
            return response()->json([
                'message' => 'チームが見つかりません',
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $users = $action->execute($team);

        return response()->json(['data' => $users], 200, [], JSON_UNESCAPED_UNICODE);
    }

    #[OA\Put(
        path: '/api/v1/teams/{sqid}/users/{userId}',
        tags: ['Teams'],
        summary: 'チームのユーザー情報を更新',
        description: '指定されたSqidのチームに所属するユーザーの情報を更新します',
        operationId: 'updateTeamUser',
        security: [['sanctum' => []]]
    )]
    #[OA\Parameter(
        name: 'sqid',
        in: 'path',
        required: true,
        description: 'チームSqid',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Parameter(
        name: 'userId',
        in: 'path',
        required: true,
        description: 'ユーザーID',
        schema: new OA\Schema(type: 'integer', format: 'int64')
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(ref: '#/components/schemas/UpdateTeamUserRequest')
    )]
    #[OA\Response(
        response: '200',
        description: '更新成功',
        content: new OA\JsonContent(ref: '#/components/schemas/UpdateTeamUserResponse')
    )]
    #[OA\Response(response: '401', ref: '#/components/responses/401')]
    #[OA\Response(response: '404', ref: '#/components/responses/404')]
    #[OA\Response(response: '422', ref: '#/components/responses/422')]
    public function updateUser(string $sqid, int $userId, UpdateTeamUserRequest $request, UpdateTeamUserAction $action, GetTeamAction $getTeamAction): JsonResponse
    {
        $teamId = Team::findBySqid($sqid)?->id;

        if ($teamId === null) {
            return response()->json([
                'message' => 'チームが見つかりませんでした',
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $team = $getTeamAction->execute($teamId);

        if ($team === null) {
            return response()->json([
                'message' => 'チームが見つかりません',
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $teamUser = $action->execute($team, $userId, $request->validated()['is_owner']);

        if ($teamUser === null) {
            return response()->json([
                'message' => 'ユーザーがチームに登録されていません',
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        return response()->json($teamUser, 200, [], JSON_UNESCAPED_UNICODE);
    }

    #[OA\Delete(
        path: '/api/v1/teams/{sqid}/users/{userId}',
        tags: ['Teams'],
        summary: 'チームからユーザーを削除',
        description: '指定されたSqidのチームからユーザーを削除します',
        operationId: 'removeUserFromTeam',
        security: [['sanctum' => []]]
    )]
    #[OA\Parameter(
        name: 'sqid',
        in: 'path',
        required: true,
        description: 'チームSqid',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Parameter(
        name: 'userId',
        in: 'path',
        required: true,
        description: 'ユーザーID',
        schema: new OA\Schema(type: 'integer', format: 'int64')
    )]
    #[OA\Response(
        response: '204',
        description: '削除成功'
    )]
    #[OA\Response(response: '401', ref: '#/components/responses/401')]
    #[OA\Response(response: '404', ref: '#/components/responses/404')]
    public function removeUser(string $sqid, int $userId, RemoveUserFromTeamAction $action, GetTeamAction $getTeamAction): JsonResponse
    {
        $teamId = Team::findBySqid($sqid)?->id;

        if ($teamId === null) {
            return response()->json([
                'message' => 'チームが見つかりませんでした',
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $team = $getTeamAction->execute($teamId);

        if ($team === null) {
            return response()->json([
                'message' => 'チームが見つかりません',
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $deleted = $action->execute($team, $userId);

        if (! $deleted) {
            return response()->json([
                'message' => 'ユーザーがチームに登録されていません',
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        return response()->json(null, 204);
    }
}
