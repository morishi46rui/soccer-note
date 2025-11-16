<?php

declare(strict_types=1);

namespace Tests\Unit\UseCase\Group;

use App\Models\Group;
use App\UseCase\Group\GetGroupAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetGroupActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_a_group(): void
    {
        $group = Group::factory()->create();
        $action = new GetGroupAction;

        $result = $action->execute($group->id);

        $this->assertInstanceOf(Group::class, $result);
        $this->assertEquals($group->id, $result->id);
        $this->assertEquals($group->name, $result->name);
    }

    public function test_it_returns_null_when_group_not_found(): void
    {
        $action = new GetGroupAction;

        $result = $action->execute(999);

        $this->assertNull($result);
    }
}
