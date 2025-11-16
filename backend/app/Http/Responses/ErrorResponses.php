<?php

declare(strict_types=1);

namespace App\Http\Responses;

use OpenApi\Attributes as OA;

#[OA\Response(
    response: '401',
    description: '認証エラー',
    content: new OA\JsonContent(
        properties: [
            new OA\Property(property: 'message', type: 'string', example: '認証に失敗しました'),
        ]
    )
)]
#[OA\Response(
    response: '404',
    description: 'リソースが見つかりません',
    content: new OA\JsonContent(
        properties: [
            new OA\Property(property: 'message', type: 'string', example: 'リソースが見つかりません'),
        ]
    )
)]
#[OA\Response(
    response: '422',
    description: 'バリデーションエラー',
    content: new OA\JsonContent(
        properties: [
            new OA\Property(property: 'message', type: 'string', example: 'The given data was invalid.'),
            new OA\Property(
                property: 'errors',
                type: 'object',
                additionalProperties: new OA\AdditionalProperties(
                    type: 'array',
                    items: new OA\Items(type: 'string')
                ),
                example: [
                    'email' => ['メールアドレスは必須です。'],
                    'password' => ['パスワードは必須です。'],
                ]
            ),
        ]
    )
)]
class ErrorResponses {}
