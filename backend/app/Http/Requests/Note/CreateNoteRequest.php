<?php

namespace App\Http\Requests\Note;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'CreateNoteRequest',
    required: ['title', 'date', 'content'],
    properties: [
        new OA\Property(property: 'title', ref: '#/components/schemas/Note/properties/title'),
        new OA\Property(property: 'date', ref: '#/components/schemas/Note/properties/date'),
        new OA\Property(property: 'content', ref: '#/components/schemas/Note/properties/content'),
    ]
)]
class CreateNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'content' => ['required', 'string'],
        ];
    }
}
