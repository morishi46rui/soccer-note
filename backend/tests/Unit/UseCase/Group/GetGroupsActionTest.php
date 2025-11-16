<?php

declare(strict_types=1);

namespace Tests\Unit\UseCase\Group;

use App\Models\Group;
use App\UseCase\Group\GetGroupsAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class GetGroupsActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_paginated_groups(): void
    {
        Group::factory()->count(20)->create();
        $action = new GetGroupsAction;

        $result = $action->execute(1, 15);

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertCount(15, $result->items());
        $this->assertEquals(20, $result->total());
    }

    public function test_it_returns_groups_ordered_by_created_at_desc(): void
    {
        $group1 = Group::factory()->create(['created_at' => now()->subDays(2)]);
        $group2 = Group::factory()->create(['created_at' => now()->subDays(1)]);
        $group3 = Group::factory()->create(['created_at' => now()]);

        $action = new GetGroupsAction;
        $result = $action->execute(1, 15);

        $this->assertEquals($group3->id, $result->items()[0]->id);
        $this->assertEquals($group2->id, $result->items()[1]->id);
        $this->assertEquals($group1->id, $result->items()[2]->id);
    }

    public function test_it_returns_second_page(): void
    {
        Group::factory()->count(20)->create();
        $action = new GetGroupsAction;

        $result = $action->execute(2, 15);

        $this->assertCount(5, $result->items());
        $this->assertEquals(2, $result->currentPage());
    }
}
