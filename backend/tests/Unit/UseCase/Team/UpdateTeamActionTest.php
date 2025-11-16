<?php

declare(strict_types=1);

namespace Tests\Unit\UseCase\Team;

use App\Models\Team;
use App\UseCase\Team\UpdateTeamAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateTeamActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_updates_a_team(): void
    {
        $team = Team::factory()->create([
            'name' => '元のチーム名',
            'description' => '元の説明',
        ]);
        $action = new UpdateTeamAction;
        $data = [
            'name' => '新しいチーム名',
            'description' => '新しい説明',
        ];

        $result = $action->execute($team, $data);

        $this->assertInstanceOf(Team::class, $result);
        $this->assertEquals('新しいチーム名', $result->name);
        $this->assertEquals('新しい説明', $result->description);
        $this->assertDatabaseHas('teams', [
            'id' => $team->id,
            'name' => '新しいチーム名',
            'description' => '新しい説明',
        ]);
    }

    public function test_it_partially_updates_a_team(): void
    {
        $team = Team::factory()->create([
            'name' => '元のチーム名',
            'description' => '元の説明',
        ]);
        $action = new UpdateTeamAction;
        $data = [
            'name' => '新しいチーム名',
        ];

        $result = $action->execute($team, $data);

        $this->assertEquals('新しいチーム名', $result->name);
        $this->assertEquals('元の説明', $result->description);
    }
}
