<?php

declare(strict_types=1);

namespace App\UseCase\Group;

use App\Models\Group;

class DeleteGroupAction
{
    public function execute(Group $group): bool
    {
        return $group->delete();
    }
}
