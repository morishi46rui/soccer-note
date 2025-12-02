<?php

declare(strict_types=1);

namespace Tests\Unit\UseCase\Team;

use App\Models\Team;
use App\Models\TeamUser;
use App\Models\User;
use App\UseCase\Team\UpdateTeamUserAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateTeamUserActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_updates_team_user(): void
    {
        $action = new UpdateTeamUserAction;
        $team = Team::factory()->create();
        $user = User::factory()->create();

        $team->users()->attach($user->id, ['is_owner' => false]);

        $teamUser = $action->execute($team, $user->id, true);

        $this->assertInstanceOf(TeamUser::class, $teamUser);
        $this->assertTrue($teamUser->is_owner);
        $this->assertDatabaseHas('team_user', [
            'team_id' => $team->id,
            'user_id' => $user->id,
            'is_owner' => true,
        ]);
    }

    public function test_it_returns_null_when_user_not_in_team(): void
    {
        $action = new UpdateTeamUserAction;
        $team = Team::factory()->create();
        $user = User::factory()->create();

        $teamUser = $action->execute($team, $user->id, true);

        $this->assertNull($teamUser);
    }

    public function test_it_can_demote_owner(): void
    {
        $action = new UpdateTeamUserAction;
        $team = Team::factory()->create();
        $user = User::factory()->create();

        $team->users()->attach($user->id, ['is_owner' => true]);

        $teamUser = $action->execute($team, $user->id, false);

        $this->assertInstanceOf(TeamUser::class, $teamUser);
        $this->assertFalse($teamUser->is_owner);
        $this->assertDatabaseHas('team_user', [
            'team_id' => $team->id,
            'user_id' => $user->id,
            'is_owner' => false,
        ]);
    }
}
