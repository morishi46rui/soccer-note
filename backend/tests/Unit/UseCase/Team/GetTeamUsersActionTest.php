<?php

declare(strict_types=1);

namespace Tests\Unit\UseCase\Team;

use App\Models\Team;
use App\Models\User;
use App\UseCase\Team\GetTeamUsersAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetTeamUsersActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_team_users(): void
    {
        $action = new GetTeamUsersAction;
        $team = Team::factory()->create();
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $team->users()->attach($user1->id, ['is_owner' => true]);
        sleep(1); // 作成時刻を確実に異なる時間にする
        $team->users()->attach($user2->id, ['is_owner' => false]);

        $users = $action->execute($team);

        $this->assertCount(2, $users);
        // created_at descなので、最後に追加されたuser2が最初
        $this->assertEquals($user2->id, $users[0]['id']);
        $this->assertEquals($user1->id, $users[1]['id']);
    }

    public function test_it_returns_users_with_correct_data(): void
    {
        $action = new GetTeamUsersAction;
        $team = Team::factory()->create();
        $user = User::factory()->create();

        $team->users()->attach($user->id, ['is_owner' => true]);

        $users = $action->execute($team);

        $this->assertCount(1, $users);
        $this->assertEquals($user->id, $users[0]['id']);
        $this->assertEquals($user->sqid, $users[0]['sqid']);
        $this->assertEquals($user->name, $users[0]['name']);
        $this->assertEquals($user->email, $users[0]['email']);
        $this->assertTrue($users[0]['is_owner']);
        $this->assertNotNull($users[0]['created_at']);
    }

    public function test_it_returns_empty_collection_when_no_users(): void
    {
        $action = new GetTeamUsersAction;
        $team = Team::factory()->create();

        $users = $action->execute($team);

        $this->assertCount(0, $users);
    }
}
