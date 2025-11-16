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
        $this->seed(\Database\Seeders\PermissionSeeder::class);
        $this->seed(\Database\Seeders\RoleSeeder::class);
    }

    public function test_it_returns_all_roles(): void
    {
        // Act
        $result = $this->action->execute();

        // Assert
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(3, $result); // player, coach, admin
    }

    public function test_it_returns_roles_with_permissions(): void
    {
        // Act
        $result = $this->action->execute();

        // Assert
        /** @var Role $role */
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
        $this->assertCount(4, $playerRole->permissions); // view_notes, view_team, view_group, view_members

        $permissionNames = $playerRole->permissions->pluck('name')->toArray();
        $this->assertContains('view_notes', $permissionNames);
        $this->assertContains('view_team', $permissionNames);
        $this->assertContains('view_group', $permissionNames);
        $this->assertContains('view_members', $permissionNames);
    }

    public function test_it_returns_coach_role_with_correct_permissions(): void
    {
        // Act
        $result = $this->action->execute();
        $coachRole = $result->firstWhere('name', 'coach');

        // Assert
        $this->assertNotNull($coachRole);
        $this->assertEquals('コーチ', $coachRole->display_name);
        $this->assertCount(11, $coachRole->permissions);

        $permissionNames = $coachRole->permissions->pluck('name')->toArray();
        // ノート関連
        $this->assertContains('view_notes', $permissionNames);
        $this->assertContains('create_notes', $permissionNames);
        $this->assertContains('edit_notes', $permissionNames);
        $this->assertContains('delete_notes', $permissionNames);
        // チーム閲覧
        $this->assertContains('view_team', $permissionNames);
        // グループ全操作
        $this->assertContains('view_group', $permissionNames);
        $this->assertContains('create_group', $permissionNames);
        $this->assertContains('edit_group', $permissionNames);
        $this->assertContains('delete_group', $permissionNames);
        // メンバー閲覧・編集
        $this->assertContains('view_members', $permissionNames);
        $this->assertContains('edit_members', $permissionNames);
    }

    public function test_it_returns_admin_role_with_all_permissions(): void
    {
        // Act
        $result = $this->action->execute();
        $adminRole = $result->firstWhere('name', 'admin');

        // Assert
        $this->assertNotNull($adminRole);
        $this->assertEquals('管理者', $adminRole->display_name);
        $this->assertCount(16, $adminRole->permissions); // すべての権限

        $permissionNames = $adminRole->permissions->pluck('name')->toArray();
        // ノート関連
        $this->assertContains('view_notes', $permissionNames);
        $this->assertContains('create_notes', $permissionNames);
        $this->assertContains('edit_notes', $permissionNames);
        $this->assertContains('delete_notes', $permissionNames);
        // チーム関連
        $this->assertContains('view_team', $permissionNames);
        $this->assertContains('create_team', $permissionNames);
        $this->assertContains('edit_team', $permissionNames);
        $this->assertContains('delete_team', $permissionNames);
        // グループ関連
        $this->assertContains('view_group', $permissionNames);
        $this->assertContains('create_group', $permissionNames);
        $this->assertContains('edit_group', $permissionNames);
        $this->assertContains('delete_group', $permissionNames);
        // メンバー関連
        $this->assertContains('view_members', $permissionNames);
        $this->assertContains('add_members', $permissionNames);
        $this->assertContains('edit_members', $permissionNames);
        $this->assertContains('remove_members', $permissionNames);
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
