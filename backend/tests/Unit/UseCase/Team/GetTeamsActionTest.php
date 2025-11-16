<?php

declare(strict_types=1);

namespace Tests\Unit\UseCase\Team;

use App\Models\Team;
use App\UseCase\Team\GetTeamsAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class GetTeamsActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_paginated_teams(): void
    {
        Team::factory()->count(5)->create();
        $action = new GetTeamsAction;

        $result = $action->execute(1, 15);

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertCount(5, $result);
    }

    public function test_it_paginates_teams_correctly(): void
    {
        Team::factory()->count(20)->create();
        $action = new GetTeamsAction;

        $result = $action->execute(2, 5);

        $this->assertEquals(2, $result->currentPage());
        $this->assertEquals(5, $result->perPage());
        $this->assertCount(5, $result);
    }

    public function test_it_orders_teams_by_created_at_desc(): void
    {
        $oldTeam = Team::factory()->create(['created_at' => now()->subDays(2)]);
        $newTeam = Team::factory()->create(['created_at' => now()]);
        $action = new GetTeamsAction;

        $result = $action->execute(1, 15);

        $this->assertEquals($newTeam->id, $result->first()->id);
        $this->assertEquals($oldTeam->id, $result->last()->id);
    }
}
