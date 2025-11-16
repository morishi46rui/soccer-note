<?php

declare(strict_types=1);

namespace Tests\Unit\UseCase\Team;

use App\Models\Team;
use App\UseCase\Team\GetTeamAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetTeamActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_a_team(): void
    {
        $team = Team::factory()->create();
        $action = new GetTeamAction;

        $result = $action->execute($team->id);

        $this->assertInstanceOf(Team::class, $result);
        $this->assertEquals($team->id, $result->id);
    }

    public function test_it_returns_null_when_team_not_found(): void
    {
        $action = new GetTeamAction;

        $result = $action->execute(99999);

        $this->assertNull($result);
    }
}
