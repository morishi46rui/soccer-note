<?php

declare(strict_types=1);

namespace App\UseCase\Group;

use App\Models\Group;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'CreateGroupResponse',
    properties: [
        new OA\Property(property: 'id', ref: '#/components/schemas/Group/properties/id'),
        new OA\Property(property: 'sqid', ref: '#/components/schemas/Group/properties/sqid'),
        new OA\Property(property: 'team_id', ref: '#/components/schemas/Group/properties/team_id'),
        new OA\Property(property: 'name', ref: '#/components/schemas/Group/properties/name'),
        new OA\Property(property: 'description', ref: '#/components/schemas/Group/properties/description'),
        new OA\Property(property: 'created_at', ref: '#/components/schemas/Group/properties/created_at'),
        new OA\Property(property: 'updated_at', ref: '#/components/schemas/Group/properties/updated_at'),
    ]
)]
class CreateGroupAction
{
    public function execute(array $data): Group
    {
        return Group::create([
            'team_id' => $data['team_id'],
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]);
    }
}
