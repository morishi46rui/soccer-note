<?php

declare(strict_types=1);

namespace Tests\Unit\UseCase\Team;

use App\Models\Team;
use App\UseCase\Team\DeleteTeamAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteTeamActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_deletes_a_team(): void
    {
        $team = Team::factory()->create();
        $action = new DeleteTeamAction;

        $result = $action->execute($team);

        $this->assertTrue($result);
        $this->assertSoftDeleted('teams', ['id' => $team->id]);
    }
}
