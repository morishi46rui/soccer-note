<?php

declare(strict_types=1);

namespace App\Http\Requests\Group;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'UpdateGroupRequest',
    properties: [
        new OA\Property(property: 'team_id', ref: '#/components/schemas/Group/properties/team_id'),
        new OA\Property(property: 'name', ref: '#/components/schemas/Group/properties/name'),
        new OA\Property(property: 'description', ref: '#/components/schemas/Group/properties/description'),
    ]
)]
class UpdateGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'team_id' => ['sometimes', 'required', 'integer', 'exists:teams,id'],
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ];
    }
}
