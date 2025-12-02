<?php

declare(strict_types=1);

namespace App\UseCase\Team;

use App\Models\Team;
use App\Models\TeamUser;
use App\Models\User;
use Illuminate\Support\Collection;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'TeamUserItem',
    properties: [
        new OA\Property(property: 'id', type: 'integer', format: 'int64', description: 'ユーザーID'),
        new OA\Property(property: 'sqid', type: 'string', description: 'ユーザーSqid'),
        new OA\Property(property: 'name', type: 'string', description: 'ユーザー名'),
        new OA\Property(property: 'email', type: 'string', format: 'email', description: 'メールアドレス'),
        new OA\Property(property: 'is_owner', type: 'boolean', description: 'オーナーフラグ'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', description: '参加日時'),
    ]
)]
#[OA\Schema(
    schema: 'GetTeamUsersResponse',
    properties: [
        new OA\Property(
            property: 'data',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/TeamUserItem')
        ),
    ]
)]
class GetTeamUsersAction
{
    public function execute(Team $team): Collection
    {
        /** @var \Illuminate\Database\Eloquent\Collection<int, User> $users */
        $users = $team->users()
            ->withPivot('is_owner', 'created_at')
            ->orderBy('team_user.created_at', 'desc')
            ->get();

        return $users->map(
            /** @param User $user */
            function ($user) {
                /** @var TeamUser $pivot */
                // @phpstan-ignore-next-line
                $pivot = $user->pivot;

                return [
                    'id' => $user->id,
                    'sqid' => $user->sqid,
                    'name' => $user->name,
                    'email' => $user->email,
                    'is_owner' => $pivot->is_owner,
                    'created_at' => $pivot->created_at,
                ];
            }
        );
    }
}
