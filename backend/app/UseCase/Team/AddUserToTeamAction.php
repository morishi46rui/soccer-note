<?php

declare(strict_types=1);

namespace App\UseCase\Team;

use App\Models\Team;
use App\Models\TeamUser;
use App\Models\User;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'AddUserToTeamResponse',
    properties: [
        new OA\Property(property: 'team_id', type: 'integer', format: 'int64', description: 'チームID'),
        new OA\Property(property: 'user_id', type: 'integer', format: 'int64', description: 'ユーザーID'),
        new OA\Property(property: 'is_owner', type: 'boolean', description: 'オーナーフラグ'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', description: '作成日時'),
    ]
)]
class AddUserToTeamAction
{
    public function execute(Team $team, string $email, bool $isOwner = false): ?TeamUser
    {
        // メールアドレスからユーザーを取得
        $user = User::where('email', $email)->first();

        if ($user === null) {
            return null;
        }

        // すでに登録されているかチェック
        $existingTeamUser = TeamUser::where('team_id', $team->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existingTeamUser !== null) {
            return null;
        }

        return TeamUser::create([
            'team_id' => $team->id,
            'user_id' => $user->id,
            'is_owner' => $isOwner,
        ]);
    }
}
