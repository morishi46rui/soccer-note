<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Group;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GroupTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_has_fillable_attributes(): void
    {
        $group = new Group;

        $this->assertEquals(
            ['team_id', 'name', 'description'],
            $group->getFillable()
        );
    }

    public function test_it_appends_sqid(): void
    {
        $group = Group::factory()->create();

        $this->assertArrayHasKey('sqid', $group->toArray());
        $this->assertIsString($group->sqid);
    }

    public function test_it_belongs_to_team(): void
    {
        $team = Team::factory()->create();
        $group = Group::factory()->create(['team_id' => $team->id]);

        $this->assertInstanceOf(Team::class, $group->team);
        $this->assertEquals($team->id, $group->team->id);
    }

    public function test_it_belongs_to_many_users(): void
    {
        $group = Group::factory()->create();
        $user = User::factory()->create();

        $group->users()->attach($user->id);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $group->users);
        $this->assertCount(1, $group->users);
        $this->assertInstanceOf(User::class, $group->users->first());
    }

    public function test_it_has_timestamps_in_pivot(): void
    {
        $group = Group::factory()->create();
        $user = User::factory()->create();

        $group->users()->attach($user->id);

        $groupUser = $group->users->first();
        $this->assertNotNull($groupUser->pivot->created_at);
        $this->assertNotNull($groupUser->pivot->updated_at);
    }

    public function test_it_uses_has_sqid_trait(): void
    {
        $group = Group::factory()->create();

        $this->assertNotNull($group->sqid);
        $this->assertEquals($group->id, Group::findBySqid($group->sqid)->id);
    }

    public function test_it_uses_has_factory_trait(): void
    {
        $group = Group::factory()->create();

        $this->assertInstanceOf(Group::class, $group);
        $this->assertDatabaseHas('groups', ['id' => $group->id]);
    }
}
