<?php

declare(strict_types=1);

namespace App\UseCase\Team;

use App\Models\Team;
use App\Models\TeamUser;

class RemoveUserFromTeamAction
{
    public function execute(Team $team, int $userId): bool
    {
        $teamUser = TeamUser::where('team_id', $team->id)
            ->where('user_id', $userId)
            ->first();

        if ($teamUser === null) {
            return false;
        }

        return (bool) $teamUser->delete();
    }
}
