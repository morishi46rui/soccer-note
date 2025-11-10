<?php

declare(strict_types=1);

namespace App\UseCase\Auth;

use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'LogoutResponse',
    properties: [
        new OA\Property(property: 'message', type: 'string', example: 'ログアウトしました'),
    ]
)]
class LogoutAction
{
    public function execute(Request $request): array
    {
        $request->user()->currentAccessToken()->delete();

        return [
            'message' => 'ログアウトしました',
        ];
    }
}
