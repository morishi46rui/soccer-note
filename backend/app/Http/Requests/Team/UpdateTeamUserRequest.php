<?php

declare(strict_types=1);

namespace App\Http\Requests\Team;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'UpdateTeamUserRequest',
    required: ['is_owner'],
    properties: [
        new OA\Property(property: 'is_owner', type: 'boolean', description: 'オーナーフラグ'),
    ]
)]
class UpdateTeamUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'is_owner' => ['required', 'boolean'],
        ];
    }
}
