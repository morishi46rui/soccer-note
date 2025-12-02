<?php

declare(strict_types=1);

namespace Tests\Unit\UseCase\Team;

use App\Models\Team;
use App\Models\User;
use App\UseCase\Team\RemoveUserFromTeamAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RemoveUserFromTeamActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_removes_user_from_team(): void
    {
        $action = new RemoveUserFromTeamAction;
        $team = Team::factory()->create();
        $user = User::factory()->create();

        $team->users()->attach($user->id, ['is_owner' => false]);

        $result = $action->execute($team, $user->id);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('team_user', [
            'team_id' => $team->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_it_returns_false_when_user_not_in_team(): void
    {
        $action = new RemoveUserFromTeamAction;
        $team = Team::factory()->create();
        $user = User::factory()->create();

        $result = $action->execute($team, $user->id);

        $this->assertFalse($result);
    }

    public function test_it_removes_owner_from_team(): void
    {
        $action = new RemoveUserFromTeamAction;
        $team = Team::factory()->create();
        $user = User::factory()->create();

        $team->users()->attach($user->id, ['is_owner' => true]);

        $result = $action->execute($team, $user->id);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('team_user', [
            'team_id' => $team->id,
            'user_id' => $user->id,
        ]);
    }
}
