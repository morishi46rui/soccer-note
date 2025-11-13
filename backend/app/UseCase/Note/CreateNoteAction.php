<?php

declare(strict_types=1);

namespace App\UseCase\Note;

use App\Models\Note;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'CreateNoteResponse',
    properties: [
        new OA\Property(property: 'id', ref: '#/components/schemas/Note/properties/id'),
        new OA\Property(property: 'user_id', ref: '#/components/schemas/Note/properties/user_id'),
        new OA\Property(property: 'title', ref: '#/components/schemas/Note/properties/title'),
        new OA\Property(property: 'date', ref: '#/components/schemas/Note/properties/date'),
        new OA\Property(property: 'content', ref: '#/components/schemas/Note/properties/content'),
        new OA\Property(property: 'created_at', ref: '#/components/schemas/Note/properties/created_at'),
        new OA\Property(property: 'updated_at', ref: '#/components/schemas/Note/properties/updated_at'),
    ]
)]
class CreateNoteAction
{
    public function execute(int $userId, array $data): Note
    {
        return Note::create([
            'user_id' => $userId,
            'title' => $data['title'],
            'date' => $data['date'],
            'content' => $data['content'],
        ]);
    }
}
