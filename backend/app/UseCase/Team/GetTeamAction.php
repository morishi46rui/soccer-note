<?php

declare(strict_types=1);

namespace App\UseCase\Team;

use App\Models\Team;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'GetTeamResponse',
    properties: [
        new OA\Property(property: 'id', ref: '#/components/schemas/Team/properties/id'),
        new OA\Property(property: 'sqid', ref: '#/components/schemas/Team/properties/sqid'),
        new OA\Property(property: 'name', ref: '#/components/schemas/Team/properties/name'),
        new OA\Property(property: 'description', ref: '#/components/schemas/Team/properties/description'),
        new OA\Property(property: 'created_at', ref: '#/components/schemas/Team/properties/created_at'),
        new OA\Property(property: 'updated_at', ref: '#/components/schemas/Team/properties/updated_at'),
    ]
)]
class GetTeamAction
{
    public function execute(int $teamId): ?Team
    {
        return Team::find($teamId);
    }
}
