<?php

declare(strict_types=1);

namespace App\UseCase\Team;

use App\Models\Team;
use Illuminate\Pagination\LengthAwarePaginator;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'GetTeamsResponse',
    properties: [
        new OA\Property(
            property: 'data',
            type: 'array',
            items: new OA\Items(
                properties: [
                    new OA\Property(property: 'id', ref: '#/components/schemas/Team/properties/id'),
                    new OA\Property(property: 'sqid', ref: '#/components/schemas/Team/properties/sqid'),
                    new OA\Property(property: 'name', ref: '#/components/schemas/Team/properties/name'),
                    new OA\Property(property: 'description', ref: '#/components/schemas/Team/properties/description'),
                    new OA\Property(property: 'created_at', ref: '#/components/schemas/Team/properties/created_at'),
                    new OA\Property(property: 'updated_at', ref: '#/components/schemas/Team/properties/updated_at'),
                ],
                type: 'object'
            )
        ),
        new OA\Property(property: 'current_page', type: 'integer', example: 1),
        new OA\Property(property: 'last_page', type: 'integer', example: 5),
        new OA\Property(property: 'per_page', type: 'integer', example: 15),
        new OA\Property(property: 'total', type: 'integer', example: 72),
    ]
)]
class GetTeamsAction
{
    public function execute(int $page = 1, int $perPage = 15): LengthAwarePaginator
    {
        $query = Team::orderBy('created_at', 'desc');

        return $query->paginate($perPage, ['*'], 'page', $page);
    }
}
