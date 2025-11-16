<?php

declare(strict_types=1);

namespace Tests\Unit\UseCase\Group;

use App\Models\Group;
use App\Models\Team;
use App\UseCase\Group\UpdateGroupAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateGroupActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_updates_a_group(): void
    {
        $group = Group::factory()->create([
            'name' => 'Oldグループ',
            'description' => 'Old description',
        ]);
        $action = new UpdateGroupAction;

        $data = [
            'name' => 'Newグループ',
            'description' => 'New description',
        ];

        $updatedGroup = $action->execute($group, $data);

        $this->assertInstanceOf(Group::class, $updatedGroup);
        $this->assertEquals('Newグループ', $updatedGroup->name);
        $this->assertEquals('New description', $updatedGroup->description);
        $this->assertDatabaseHas('groups', [
            'id' => $group->id,
            'name' => 'Newグループ',
            'description' => 'New description',
        ]);
    }

    public function test_it_updates_group_team_id(): void
    {
        $team1 = Team::factory()->create();
        $team2 = Team::factory()->create();
        $group = Group::factory()->create(['team_id' => $team1->id]);

        $action = new UpdateGroupAction;
        $data = ['team_id' => $team2->id];

        $updatedGroup = $action->execute($group, $data);

        $this->assertEquals($team2->id, $updatedGroup->team_id);
        $this->assertDatabaseHas('groups', [
            'id' => $group->id,
            'team_id' => $team2->id,
        ]);
    }

    public function test_it_returns_fresh_instance(): void
    {
        $group = Group::factory()->create(['name' => 'Oldグループ']);
        $action = new UpdateGroupAction;

        $updatedGroup = $action->execute($group, ['name' => 'Newグループ']);

        $this->assertNotSame($group, $updatedGroup);
        $this->assertEquals('Newグループ', $updatedGroup->name);
    }
}
