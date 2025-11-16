<?php

declare(strict_types=1);

namespace App\UseCase\Auth;

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

        $deviceName = $deviceName ?? 'unknown-device';
        $token = $user->createToken($deviceName)->plainTextToken;

        return [
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ];
    }
}
