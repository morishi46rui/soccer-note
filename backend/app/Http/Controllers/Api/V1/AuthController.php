<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\UpdatePasswordRequest;
use App\Http\Requests\Auth\UpdateProfileRequest;
use App\UseCase\Auth\GetProfileAction;
use App\UseCase\Auth\LoginAction;
use App\UseCase\Auth\LogoutAction;
use App\UseCase\Auth\RegisterAction;
use App\UseCase\Auth\UpdatePasswordAction;
use App\UseCase\Auth\UpdateProfileAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Auth', description: '認証')]
class AuthController extends Controller
{
    #[OA\Post(
        path: '/api/v1/auth/register',
        tags: ['Auth'],
        summary: 'ユーザー登録',
        description: '新規ユーザーを登録します',
        operationId: 'register'
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(ref: '#/components/schemas/RegisterRequest')
    )]
    #[OA\Response(
        response: '201',
        description: '登録成功',
        content: new OA\JsonContent(ref: '#/components/schemas/RegisterResponse')
    )]
    #[OA\Response(response: '422', description: 'バリデーションエラー')]
    public function register(RegisterRequest $request, RegisterAction $action): JsonResponse
    {
        $result = $action->execute(
            $request->name,
            $request->email,
            $request->password
        );

        return response()->json($result, 201, [], JSON_UNESCAPED_UNICODE);
    }

    #[OA\Post(
        path: '/api/v1/auth/login',
        tags: ['Auth'],
        summary: 'ログイン',
        description: 'ユーザーログインしてトークンを発行します',
        operationId: 'login'
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(ref: '#/components/schemas/LoginRequest')
    )]
    #[OA\Response(
        response: '200',
        description: 'ログイン成功',
        content: new OA\JsonContent(ref: '#/components/schemas/LoginResponse')
    )]
    #[OA\Response(response: '401', description: '認証失敗')]
    public function login(LoginRequest $request, LoginAction $action): JsonResponse
    {
        $result = $action->execute(
            $request->email,
            $request->password,
            $request->device_name
        );

        if ($result === null) {
            return response()->json([
                'message' => '認証に失敗しました',
            ], 401, [], JSON_UNESCAPED_UNICODE);
        }

        return response()->json($result, 200, [], JSON_UNESCAPED_UNICODE);
    }

    #[OA\Post(
        path: '/api/v1/auth/logout',
        tags: ['Auth'],
        summary: 'ログアウト',
        description: '現在のトークンを無効化します',
        operationId: 'logout',
        security: [['sanctum' => []]]
    )]
    #[OA\Response(
        response: '200',
        description: 'ログアウト成功',
        content: new OA\JsonContent(ref: '#/components/schemas/LogoutResponse')
    )]
    #[OA\Response(response: '401', description: '認証エラー')]
    public function logout(Request $request, LogoutAction $action): JsonResponse
    {
        $result = $action->execute($request);

        return response()->json($result, 200, [], JSON_UNESCAPED_UNICODE);
    }

    #[OA\Get(
        path: '/api/v1/auth/me',
        tags: ['Auth'],
        summary: '認証ユーザー情報取得',
        description: '現在認証されているユーザーの情報を取得します',
        operationId: 'me',
        security: [['sanctum' => []]]
    )]
    #[OA\Response(
        response: '200',
        description: '成功',
        content: new OA\JsonContent(ref: '#/components/schemas/GetProfileResponse')
    )]
    #[OA\Response(response: '401', description: '認証エラー')]
    public function me(Request $request, GetProfileAction $action): JsonResponse
    {
        $result = $action->execute($request->user());

        return response()->json($result, 200, [], JSON_UNESCAPED_UNICODE);
    }

    #[OA\Put(
        path: '/api/v1/auth/profile',
        tags: ['Auth'],
        summary: 'プロフィール更新',
        description: '認証ユーザーのプロフィール情報を更新します',
        operationId: 'updateProfile',
        security: [['sanctum' => []]]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(ref: '#/components/schemas/UpdateProfileRequest')
    )]
    #[OA\Response(
        response: '200',
        description: '更新成功',
        content: new OA\JsonContent(ref: '#/components/schemas/UpdateProfileResponse')
    )]
    #[OA\Response(response: '401', description: '認証エラー')]
    #[OA\Response(response: '422', description: 'バリデーションエラー')]
    public function updateProfile(UpdateProfileRequest $request, UpdateProfileAction $action): JsonResponse
    {
        $result = $action->execute($request->user(), $request->validated());

        return response()->json($result, 200, [], JSON_UNESCAPED_UNICODE);
    }

    #[OA\Put(
        path: '/api/v1/auth/password',
        tags: ['Auth'],
        summary: 'パスワード変更',
        description: '認証ユーザーのパスワードを変更します',
        operationId: 'updatePassword',
        security: [['sanctum' => []]]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(ref: '#/components/schemas/UpdatePasswordRequest')
    )]
    #[OA\Response(
        response: '200',
        description: '更新成功',
        content: new OA\JsonContent(ref: '#/components/schemas/UpdatePasswordResponse')
    )]
    #[OA\Response(response: '401', description: '認証エラー')]
    #[OA\Response(response: '422', description: 'バリデーションエラー')]
    public function updatePassword(UpdatePasswordRequest $request, UpdatePasswordAction $action): JsonResponse
    {
        $result = $action->execute($request->user(), $request->password);

        return response()->json($result, 200, [], JSON_UNESCAPED_UNICODE);
    }
}
