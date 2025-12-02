<?php

declare(strict_types=1);

namespace App\UseCase\Team;

use App\Models\Team;
use App\Models\TeamUser;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'UpdateTeamUserResponse',
    properties: [
        new OA\Property(property: 'team_id', type: 'integer', format: 'int64', description: 'チームID'),
        new OA\Property(property: 'user_id', type: 'integer', format: 'int64', description: 'ユーザーID'),
        new OA\Property(property: 'is_owner', type: 'boolean', description: 'オーナーフラグ'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', description: '更新日時'),
    ]
)]
class UpdateTeamUserAction
{
    public function execute(Team $team, int $userId, bool $isOwner): ?TeamUser
    {
        $teamUser = TeamUser::where('team_id', $team->id)
            ->where('user_id', $userId)
            ->first();

        if ($teamUser === null) {
            return null;
        }

        $teamUser->is_owner = $isOwner;
        $teamUser->save();

        return $teamUser;
    }
}
