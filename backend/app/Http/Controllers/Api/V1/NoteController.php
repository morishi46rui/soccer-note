<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Note\CreateNoteRequest;
use App\Http\Requests\Note\UpdateNoteRequest;
use App\Models\Note;
use App\UseCase\Note\CreateNoteAction;
use App\UseCase\Note\DeleteNoteAction;
use App\UseCase\Note\GetNoteAction;
use App\UseCase\Note\GetNotesAction;
use App\UseCase\Note\UpdateNoteAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Notes', description: 'ノート管理')]
class NoteController extends Controller
{
    #[OA\Get(
        path: '/api/v1/notes',
        tags: ['Notes'],
        summary: 'ノート一覧取得',
        description: 'ログインユーザーのノート一覧を取得します',
        operationId: 'getNotes',
        security: [['sanctum' => []]]
    )]
    #[OA\Parameter(ref: '#/components/parameters/page')]
    #[OA\Parameter(ref: '#/components/parameters/per_page')]
    #[OA\Response(
        response: '200',
        description: '取得成功',
        content: new OA\JsonContent(ref: '#/components/schemas/GetNotesResponse')
    )]
    #[OA\Response(response: '401', ref: '#/components/responses/401')]
    public function index(Request $request, GetNotesAction $action): JsonResponse
    {
        $page = (int) $request->get('page', 1);
        $perPage = (int) $request->get('per_page', 15);
        $notes = $action->execute($request->user()->id, $page, $perPage);

        return response()->json($notes, 200, [], JSON_UNESCAPED_UNICODE);
    }

    #[OA\Post(
        path: '/api/v1/notes',
        tags: ['Notes'],
        summary: 'ノート作成',
        description: '新しいノートを作成します',
        operationId: 'createNote',
        security: [['sanctum' => []]]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(ref: '#/components/schemas/CreateNoteRequest')
    )]
    #[OA\Response(
        response: '201',
        description: '作成成功',
        content: new OA\JsonContent(ref: '#/components/schemas/CreateNoteResponse')
    )]
    #[OA\Response(response: '401', ref: '#/components/responses/401')]
    #[OA\Response(response: '422', ref: '#/components/responses/422')]
    public function store(CreateNoteRequest $request, CreateNoteAction $action): JsonResponse
    {
        $note = $action->execute($request->user()->id, $request->validated());

        return response()->json($note, 201, [], JSON_UNESCAPED_UNICODE);
    }

    #[OA\Get(
        path: '/api/v1/notes/{id}',
        tags: ['Notes'],
        summary: 'ノート詳細取得',
        description: '指定されたSqidまたはIDのノート詳細を取得します',
        operationId: 'getNote',
        security: [['sanctum' => []]]
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        required: true,
        description: 'ノートSqidまたはID',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Response(
        response: '200',
        description: '取得成功',
        content: new OA\JsonContent(ref: '#/components/schemas/GetNoteResponse')
    )]
    #[OA\Response(response: '401', ref: '#/components/responses/401')]
    #[OA\Response(response: '404', ref: '#/components/responses/404')]
    public function show(string $id, Request $request, GetNoteAction $action): JsonResponse
    {
        // SqidまたはIDを受け入れる
        $noteId = is_numeric($id) ? (int) $id : Note::findBySqid($id)?->id;

        if ($noteId === null) {
            return response()->json([
                'message' => 'ノートが見つかりませんでした',
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $note = $action->execute($noteId, $request->user()->id);

        if ($note === null) {
            return response()->json([
                'message' => 'ノートが見つかりません',
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        return response()->json($note, 200, [], JSON_UNESCAPED_UNICODE);
    }

    #[OA\Put(
        path: '/api/v1/notes/{id}',
        tags: ['Notes'],
        summary: 'ノート更新',
        description: '指定されたSqidまたはIDのノートを更新します',
        operationId: 'updateNote',
        security: [['sanctum' => []]]
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        required: true,
        description: 'ノートSqidまたはID',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(ref: '#/components/schemas/UpdateNoteRequest')
    )]
    #[OA\Response(
        response: '200',
        description: '更新成功',
        content: new OA\JsonContent(ref: '#/components/schemas/UpdateNoteResponse')
    )]
    #[OA\Response(response: '401', ref: '#/components/responses/401')]
    #[OA\Response(response: '404', ref: '#/components/responses/404')]
    #[OA\Response(response: '422', ref: '#/components/responses/422')]
    public function update(string $id, UpdateNoteRequest $request, UpdateNoteAction $action, GetNoteAction $getNoteAction): JsonResponse
    {
        // SqidまたはIDを受け入れる
        $noteId = is_numeric($id) ? (int) $id : Note::findBySqid($id)?->id;

        if ($noteId === null) {
            return response()->json([
                'message' => 'ノートが見つかりませんでした',
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $note = $getNoteAction->execute($noteId, $request->user()->id);

        if ($note === null) {
            return response()->json([
                'message' => 'ノートが見つかりません',
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $updatedNote = $action->execute($note, $request->validated());

        return response()->json($updatedNote, 200, [], JSON_UNESCAPED_UNICODE);
    }

    #[OA\Delete(
        path: '/api/v1/notes/{id}',
        tags: ['Notes'],
        summary: 'ノート削除',
        description: '指定されたSqidまたはIDのノートを削除します',
        operationId: 'deleteNote',
        security: [['sanctum' => []]]
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        required: true,
        description: 'ノートSqidまたはID',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Response(
        response: '204',
        description: '削除成功'
    )]
    #[OA\Response(response: '401', ref: '#/components/responses/401')]
    #[OA\Response(response: '404', ref: '#/components/responses/404')]
    public function destroy(string $id, Request $request, DeleteNoteAction $action, GetNoteAction $getNoteAction): JsonResponse
    {
        // SqidまたはIDを受け入れる
        $noteId = is_numeric($id) ? (int) $id : Note::findBySqid($id)?->id;

        if ($noteId === null) {
            return response()->json([
                'message' => 'ノートが見つかりませんでした',
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $note = $getNoteAction->execute($noteId, $request->user()->id);

        if ($note === null) {
            return response()->json([
                'message' => 'ノートが見つかりません',
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $action->execute($note);

        return response()->json(null, 204);
    }
}
