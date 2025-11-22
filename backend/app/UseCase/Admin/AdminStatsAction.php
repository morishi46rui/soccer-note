<?php

declare(strict_types=1);

namespace App\UseCase\Admin;

use App\Models\Group;
use App\Models\Note;
use App\Models\Team;
use App\Models\User;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'AdminStatsResponse',
    properties: [
        new OA\Property(property: 'total_users', type: 'integer', description: '総ユーザー数'),
        new OA\Property(property: 'active_users', type: 'integer', description: 'アクティブユーザー数'),
        new OA\Property(property: 'total_teams', type: 'integer', description: '総チーム数'),
        new OA\Property(property: 'total_groups', type: 'integer', description: '総グループ数'),
        new OA\Property(property: 'total_posts', type: 'integer', description: '総投稿数'),
    ]
)]

class AdminStatsAction
{
    public function execute(): array
    {
        return [
            'total_users' => User::count(),
            'active_users' => User::whereNotNull('email_verified_at')->count(),
            'total_teams' => Team::count(),
            'total_groups' => Group::count(),
            'total_posts' => Note::count(),
        ];
    }
}
