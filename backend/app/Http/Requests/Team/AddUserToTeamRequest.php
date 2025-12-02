<?php

declare(strict_types=1);

namespace App\Http\Requests\Team;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'AddUserToTeamRequest',
    required: ['email'],
    properties: [
        new OA\Property(property: 'email', type: 'string', format: 'email', description: 'ユーザーのメールアドレス'),
        new OA\Property(property: 'is_owner', type: 'boolean', description: 'オーナーフラグ', default: false),
    ]
)]
class AddUserToTeamRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'exists:users,email'],
            'is_owner' => ['nullable', 'boolean'],
        ];
    }
}
