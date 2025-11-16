<?php

declare(strict_types=1);

namespace Tests\Unit\UseCase\Team;

use App\Models\Team;
use App\UseCase\Team\CreateTeamAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateTeamActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_team(): void
    {
        $action = new CreateTeamAction;
        $data = [
            'name' => 'FC東京',
            'description' => '東京のサッカーチーム',
        ];

        $team = $action->execute($data);

        $this->assertInstanceOf(Team::class, $team);
        $this->assertEquals('FC東京', $team->name);
        $this->assertEquals('東京のサッカーチーム', $team->description);
        $this->assertDatabaseHas('teams', [
            'name' => 'FC東京',
            'description' => '東京のサッカーチーム',
        ]);
    }

    public function test_it_creates_a_team_without_description(): void
    {
        $action = new CreateTeamAction;
        $data = [
            'name' => 'FC大阪',
        ];

        $team = $action->execute($data);

        $this->assertInstanceOf(Team::class, $team);
        $this->assertEquals('FC大阪', $team->name);
        $this->assertNull($team->description);
        $this->assertDatabaseHas('teams', [
            'name' => 'FC大阪',
            'description' => null,
        ]);
    }
}
