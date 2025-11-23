<?php

declare(strict_types=1);

namespace App\UseCase\Auth;

use App\Models\User;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'GetProfileResponse',
    properties: [
        new OA\Property(property: 'id', ref: '#/components/schemas/User/properties/id'),
        new OA\Property(property: 'name', ref: '#/components/schemas/User/properties/name'),
        new OA\Property(property: 'email', ref: '#/components/schemas/User/properties/email'),
        new OA\Property(property: 'email_verified_at', ref: '#/components/schemas/User/properties/email_verified_at'),
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
        new OA\Property(property: 'created_at', ref: '#/components/schemas/User/properties/created_at'),
        new OA\Property(property: 'updated_at', ref: '#/components/schemas/User/properties/updated_at'),
    ]
)]
class GetProfileAction
{
    public function execute(User $user): User
    {
        return $user->load('roles');
    }
}
