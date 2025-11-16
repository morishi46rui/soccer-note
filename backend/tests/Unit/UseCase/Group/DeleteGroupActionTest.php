<?php

declare(strict_types=1);

namespace Tests\Unit\UseCase\Group;

use App\Models\Group;
use App\UseCase\Group\DeleteGroupAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteGroupActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_deletes_a_group(): void
    {
        $group = Group::factory()->create();
        $action = new DeleteGroupAction;

        $result = $action->execute($group);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('groups', [
            'id' => $group->id,
        ]);
    }

    public function test_it_returns_true_on_successful_deletion(): void
    {
        $group = Group::factory()->create();
        $action = new DeleteGroupAction;

        $result = $action->execute($group);

        $this->assertTrue($result);
    }
}
