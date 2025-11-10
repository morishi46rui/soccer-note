<?php

declare(strict_types=1);

namespace App\UseCase\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'RegisterResponse',
    properties: [
        new OA\Property(
            property: 'user',
            properties: [
                new OA\Property(property: 'id', ref: '#/components/schemas/User/properties/id'),
                new OA\Property(property: 'name', ref: '#/components/schemas/User/properties/name'),
                new OA\Property(property: 'email', ref: '#/components/schemas/User/properties/email'),
                new OA\Property(property: 'created_at', ref: '#/components/schemas/User/properties/created_at'),
            ],
            type: 'object'
        ),
        new OA\Property(property: 'token', type: 'string', example: '1|abcdefghijklmnopqrstuvwxyz'),
    ]
)]
class RegisterAction
{
    public function execute(string $name, string $email, string $password): array
    {
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        $token = $user->createToken('auth-token')->plainTextToken;

        return [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'created_at' => $user->created_at,
            ],
            'token' => $token,
        ];
    }
}
