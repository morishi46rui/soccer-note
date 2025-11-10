<?php

declare(strict_types=1);

namespace App\UseCase\Auth;

use App\Models\User;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'UpdateProfileResponse',
    properties: [
        new OA\Property(property: 'id', ref: '#/components/schemas/User/properties/id'),
        new OA\Property(property: 'name', ref: '#/components/schemas/User/properties/name'),
        new OA\Property(property: 'email', ref: '#/components/schemas/User/properties/email'),
        new OA\Property(property: 'email_verified_at', ref: '#/components/schemas/User/properties/email_verified_at'),
        new OA\Property(property: 'created_at', ref: '#/components/schemas/User/properties/created_at'),
        new OA\Property(property: 'updated_at', ref: '#/components/schemas/User/properties/updated_at'),
    ]
)]
class UpdateProfileAction
{
    public function execute(User $user, array $data): User
    {
        $user->update($data);

        return $user;
    }
}
