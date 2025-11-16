<?php

declare(strict_types=1);

namespace App\UseCase\Team;

use App\Models\Team;

class DeleteTeamAction
{
    public function execute(Team $team): bool
    {
        return $team->delete();
    }
}
