<?php

declare(strict_types=1);

namespace Tests\Unit\UseCase\Permission;

use App\Models\Permission;
use App\UseCase\Permission\GetPermissionsAction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetPermissionsActionTest extends TestCase
{
    use RefreshDatabase;

    private GetPermissionsAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new GetPermissionsAction;

        // シーダーを実行して権限を作成
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    public function test_it_returns_all_permissions(): void
    {
        // Act
        $result = $this->action->execute();

        // Assert
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(6, $result); // 6つの権限
    }

    public function test_it_returns_correct_permission_names(): void
    {
        // Act
        $result = $this->action->execute();
        $permissionNames = $result->pluck('name')->toArray();

        // Assert
        $this->assertContains('view_notes', $permissionNames);
        $this->assertContains('edit_notes', $permissionNames);
        $this->assertContains('delete_notes', $permissionNames);
        $this->assertContains('manage_team', $permissionNames);
        $this->assertContains('manage_group', $permissionNames);
        $this->assertContains('manage_members', $permissionNames);
    }

    public function test_it_returns_permissions_with_display_names(): void
    {
        // Act
        $result = $this->action->execute();

        // Assert
        foreach ($result as $permission) {
            $this->assertNotNull($permission->name);
            $this->assertNotNull($permission->display_name);
            $this->assertIsString($permission->name);
            $this->assertIsString($permission->display_name);
        }
    }

    public function test_it_returns_view_notes_permission_with_correct_data(): void
    {
        // Act
        $result = $this->action->execute();
        $viewNotesPermission = $result->firstWhere('name', 'view_notes');

        // Assert
        $this->assertNotNull($viewNotesPermission);
        $this->assertEquals('ノート閲覧', $viewNotesPermission->display_name);
        $this->assertEquals('ノートを閲覧できる', $viewNotesPermission->description);
    }

    public function test_it_returns_manage_team_permission_with_correct_data(): void
    {
        // Act
        $result = $this->action->execute();
        $manageTeamPermission = $result->firstWhere('name', 'manage_team');

        // Assert
        $this->assertNotNull($manageTeamPermission);
        $this->assertEquals('チーム管理', $manageTeamPermission->display_name);
        $this->assertEquals('チームを管理できる', $manageTeamPermission->description);
    }

    public function test_it_returns_empty_collection_when_no_permissions_exist(): void
    {
        // Arrange - すべての権限を削除
        Permission::query()->delete();

        // Act
        $result = $this->action->execute();

        // Assert
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(0, $result);
    }
}
