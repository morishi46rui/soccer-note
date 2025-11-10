<?php

declare(strict_types=1);

namespace App\UseCase\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'UpdatePasswordResponse',
    properties: [
        new OA\Property(property: 'message', type: 'string', example: 'パスワードを更新しました'),
    ]
)]
class UpdatePasswordAction
{
    public function execute(User $user, string $password): array
    {
        $user->update([
            'password' => Hash::make($password),
        ]);

        return [
            'message' => 'パスワードを更新しました',
        ];
    }
}
