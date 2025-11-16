<?php

declare(strict_types=1);

namespace Tests\Unit\UseCase\Role;

use App\Models\Role;
use App\UseCase\Role\GetRolesAction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetRolesActionTest extends TestCase
{
    use RefreshDatabase;

    private GetRolesAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new GetRolesAction;

        // シーダーを実行してロールと権限を作成
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    public function test_it_returns_all_roles(): void
    {
        // Act
        $result = $this->action->execute();

        // Assert
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(4, $result); // player, coach, manager, admin
    }

    public function test_it_returns_roles_with_permissions(): void
    {
        // Act
        $result = $this->action->execute();

        // Assert
        foreach ($result as $role) {
            $this->assertNotNull($role->permissions);
            $this->assertInstanceOf(Collection::class, $role->permissions);
        }
    }

    public function test_it_returns_player_role_with_correct_permissions(): void
    {
        // Act
        $result = $this->action->execute();
        $playerRole = $result->firstWhere('name', 'player');

        // Assert
        $this->assertNotNull($playerRole);
        $this->assertEquals('選手', $playerRole->display_name);
        $this->assertCount(1, $playerRole->permissions);
        $this->assertEquals('view_notes', $playerRole->permissions->first()->name);
    }

    public function test_it_returns_coach_role_with_correct_permissions(): void
    {
        // Act
        $result = $this->action->execute();
        $coachRole = $result->firstWhere('name', 'coach');

        // Assert
        $this->assertNotNull($coachRole);
        $this->assertEquals('コーチ', $coachRole->display_name);
        $this->assertCount(3, $coachRole->permissions);

        $permissionNames = $coachRole->permissions->pluck('name')->toArray();
        $this->assertContains('view_notes', $permissionNames);
        $this->assertContains('edit_notes', $permissionNames);
        $this->assertContains('delete_notes', $permissionNames);
    }

    public function test_it_returns_manager_role_with_correct_permissions(): void
    {
        // Act
        $result = $this->action->execute();
        $managerRole = $result->firstWhere('name', 'manager');

        // Assert
        $this->assertNotNull($managerRole);
        $this->assertEquals('マネージャー', $managerRole->display_name);
        $this->assertCount(3, $managerRole->permissions);

        $permissionNames = $managerRole->permissions->pluck('name')->toArray();
        $this->assertContains('view_notes', $permissionNames);
        $this->assertContains('edit_notes', $permissionNames);
        $this->assertContains('manage_group', $permissionNames);
    }

    public function test_it_returns_admin_role_with_all_permissions(): void
    {
        // Act
        $result = $this->action->execute();
        $adminRole = $result->firstWhere('name', 'admin');

        // Assert
        $this->assertNotNull($adminRole);
        $this->assertEquals('管理者', $adminRole->display_name);
        $this->assertCount(6, $adminRole->permissions); // すべての権限

        $permissionNames = $adminRole->permissions->pluck('name')->toArray();
        $this->assertContains('view_notes', $permissionNames);
        $this->assertContains('edit_notes', $permissionNames);
        $this->assertContains('delete_notes', $permissionNames);
        $this->assertContains('manage_team', $permissionNames);
        $this->assertContains('manage_group', $permissionNames);
        $this->assertContains('manage_members', $permissionNames);
    }

    public function test_it_returns_empty_collection_when_no_roles_exist(): void
    {
        // Arrange - すべてのロールを削除
        Role::query()->delete();

        // Act
        $result = $this->action->execute();

        // Assert
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(0, $result);
    }
}
