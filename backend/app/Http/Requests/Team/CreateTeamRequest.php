<?php

declare(strict_types=1);

namespace App\Http\Requests\Team;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'CreateTeamRequest',
    required: ['name'],
    properties: [
        new OA\Property(property: 'name', ref: '#/components/schemas/Team/properties/name'),
        new OA\Property(property: 'description', ref: '#/components/schemas/Team/properties/description'),
    ]
)]
class CreateTeamRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ];
    }
}
