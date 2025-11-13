<?php

declare(strict_types=1);

namespace App\UseCase\Note;

use App\Models\Note;
use Illuminate\Pagination\LengthAwarePaginator;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'GetNotesResponse',
    properties: [
        new OA\Property(
            property: 'data',
            type: 'array',
            items: new OA\Items(
                properties: [
                    new OA\Property(property: 'id', ref: '#/components/schemas/Note/properties/id'),
                    new OA\Property(property: 'sqid', ref: '#/components/schemas/Note/properties/sqid'),
                    new OA\Property(property: 'user_id', ref: '#/components/schemas/Note/properties/user_id'),
                    new OA\Property(property: 'title', ref: '#/components/schemas/Note/properties/title'),
                    new OA\Property(property: 'date', ref: '#/components/schemas/Note/properties/date'),
                    new OA\Property(property: 'content', ref: '#/components/schemas/Note/properties/content'),
                    new OA\Property(property: 'created_at', ref: '#/components/schemas/Note/properties/created_at'),
                    new OA\Property(property: 'updated_at', ref: '#/components/schemas/Note/properties/updated_at'),
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
class GetNotesAction
{
    public function execute(int $userId, int $page = 1, int $perPage = 15): LengthAwarePaginator
    {
        $query = Note::where('user_id', $userId)
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc');

        return $query->paginate($perPage, ['*'], 'page', $page);
    }
}
