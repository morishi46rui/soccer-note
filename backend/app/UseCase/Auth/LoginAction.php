<?php

declare(strict_types=1);

namespace App\UseCase\Auth;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'LoginResponse',
    properties: [
        new OA\Property(property: 'token', type: 'string', example: '1|abcdefghijklmnopqrstuvwxyz'),
        new OA\Property(
            property: 'user',
            properties: [
                new OA\Property(property: 'id', ref: '#/components/schemas/User/properties/id'),
                new OA\Property(property: 'name', ref: '#/components/schemas/User/properties/name'),
                new OA\Property(property: 'email', ref: '#/components/schemas/User/properties/email'),
                new OA\Property(
                    property: 'roles',
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'id', type: 'integer'),
                            new OA\Property(property: 'name', type: 'string'),
                            new OA\Property(property: 'display_name', type: 'string'),
                        ]
                    )
                ),
            ],
            type: 'object'
        ),
    ]
)]
class LoginAction
{
    public function execute(string $email, string $password, ?string $deviceName = null): ?array
    {
        $user = User::where('email', $email)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            return null;
        }

        // ロール情報をロード
        $user->load('roles');

        $deviceName = $deviceName ?? 'unknown-device';
        $token = $user->createToken($deviceName)->plainTextToken;

        /** @var \Illuminate\Database\Eloquent\Collection<int, Role> $roles */
        $roles = $user->roles;

        return [
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $roles->map(fn (Role $role) => [
                    'id' => $role->id,
                    'name' => $role->name,
                    'display_name' => $role->display_name,
                ])->toArray(),
            ],
        ];
    }
}
