<?php

declare(strict_types=1);

namespace Tests\Unit\UseCase\Group;

use App\Models\Group;
use App\Models\Team;
use App\UseCase\Group\CreateGroupAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateGroupActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_group(): void
    {
        $team = Team::factory()->create();
        $action = new CreateGroupAction;
        $data = [
            'team_id' => $team->id,
            'name' => 'Aグループ',
            'description' => 'チームAのグループ',
        ];

        $group = $action->execute($data);

        $this->assertInstanceOf(Group::class, $group);
        $this->assertEquals($team->id, $group->team_id);
        $this->assertEquals('Aグループ', $group->name);
        $this->assertEquals('チームAのグループ', $group->description);
        $this->assertDatabaseHas('groups', [
            'team_id' => $team->id,
            'name' => 'Aグループ',
            'description' => 'チームAのグループ',
        ]);
    }

    public function test_it_creates_a_group_without_description(): void
    {
        $team = Team::factory()->create();
        $action = new CreateGroupAction;
        $data = [
            'team_id' => $team->id,
            'name' => 'Bグループ',
        ];

        $group = $action->execute($data);

        $this->assertInstanceOf(Group::class, $group);
        $this->assertEquals($team->id, $group->team_id);
        $this->assertEquals('Bグループ', $group->name);
        $this->assertNull($group->description);
        $this->assertDatabaseHas('groups', [
            'team_id' => $team->id,
            'name' => 'Bグループ',
            'description' => null,
        ]);
    }
}
