<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_has_fillable_attributes(): void
    {
        $team = new Team;

        $this->assertEquals(
            ['name', 'description'],
            $team->getFillable()
        );
    }

    public function test_it_appends_sqid(): void
    {
        $team = Team::factory()->create();

        $this->assertArrayHasKey('sqid', $team->toArray());
        $this->assertIsString($team->sqid);
    }

    public function test_it_belongs_to_many_users(): void
    {
        $team = Team::factory()->create();
        $user = User::factory()->create();
        $role = \App\Models\Role::factory()->create();

        $team->users()->attach($user->id, ['role_id' => $role->id]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $team->users);
        $this->assertCount(1, $team->users);
        $this->assertInstanceOf(User::class, $team->users->first());
    }

    public function test_it_has_pivot_data_with_role_id(): void
    {
        $team = Team::factory()->create();
        $user = User::factory()->create();
        $role = \App\Models\Role::factory()->create();

        $team->users()->attach($user->id, ['role_id' => $role->id]);

        $teamUser = $team->users->first();
        $this->assertEquals($role->id, $teamUser->pivot->role_id);
        $this->assertNotNull($teamUser->pivot->created_at);
        $this->assertNotNull($teamUser->pivot->updated_at);
    }

    public function test_it_uses_has_sqid_trait(): void
    {
        $team = Team::factory()->create();

        $this->assertNotNull($team->sqid);
        $this->assertEquals($team->id, Team::findBySqid($team->sqid)->id);
    }

    public function test_it_uses_has_factory_trait(): void
    {
        $team = Team::factory()->create();

        $this->assertInstanceOf(Team::class, $team);
        $this->assertDatabaseHas('teams', ['id' => $team->id]);
    }
}
