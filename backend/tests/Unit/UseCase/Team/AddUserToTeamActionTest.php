<?php

declare(strict_types=1);

namespace Tests\Unit\UseCase\Team;

use App\Models\Team;
use App\Models\TeamUser;
use App\Models\User;
use App\UseCase\Team\AddUserToTeamAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddUserToTeamActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_adds_a_user_to_team(): void
    {
        $action = new AddUserToTeamAction;
        $team = Team::factory()->create();
        $user = User::factory()->create();

        $teamUser = $action->execute($team, $user->email, false);

        $this->assertInstanceOf(TeamUser::class, $teamUser);
        $this->assertEquals($team->id, $teamUser->team_id);
        $this->assertEquals($user->id, $teamUser->user_id);
        $this->assertFalse($teamUser->is_owner);
        $this->assertDatabaseHas('team_user', [
            'team_id' => $team->id,
            'user_id' => $user->id,
            'is_owner' => false,
        ]);
    }

    public function test_it_adds_a_user_to_team_as_owner(): void
    {
        $action = new AddUserToTeamAction;
        $team = Team::factory()->create();
        $user = User::factory()->create();

        $teamUser = $action->execute($team, $user->email, true);

        $this->assertInstanceOf(TeamUser::class, $teamUser);
        $this->assertEquals($team->id, $teamUser->team_id);
        $this->assertEquals($user->id, $teamUser->user_id);
        $this->assertTrue($teamUser->is_owner);
        $this->assertDatabaseHas('team_user', [
            'team_id' => $team->id,
            'user_id' => $user->id,
            'is_owner' => true,
        ]);
    }

    public function test_it_returns_null_when_user_already_exists_in_team(): void
    {
        $action = new AddUserToTeamAction;
        $team = Team::factory()->create();
        $user = User::factory()->create();

        // 最初の登録
        TeamUser::create([
            'team_id' => $team->id,
            'user_id' => $user->id,
            'is_owner' => false,
        ]);

        // 重複登録を試みる
        $teamUser = $action->execute($team, $user->email, false);

        $this->assertNull($teamUser);
        $this->assertDatabaseCount('team_user', 1);
    }

    public function test_it_returns_null_when_user_not_found(): void
    {
        $action = new AddUserToTeamAction;
        $team = Team::factory()->create();

        $teamUser = $action->execute($team, 'nonexistent@example.com', false);

        $this->assertNull($teamUser);
        $this->assertDatabaseCount('team_user', 0);
    }
}
