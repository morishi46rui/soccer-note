<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Api\V1;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermissionControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();

        // シーダーを実行して権限を作成
        $this->seed(\Database\Seeders\PermissionSeeder::class);
        $this->seed(\Database\Seeders\RoleSeeder::class);
    }

    public function test_it_returns_all_permissions(): void
    {
        // Act
        $response = $this->actingAs($this->user)->getJson('/api/v1/permissions');

        // Assert
        $response->assertOk()
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'name',
                    'display_name',
                    'description',
                    'created_at',
                    'updated_at',
                ],
            ])
            ->assertJsonCount(16); // 16つの権限
    }

    public function test_it_returns_correct_permission_data(): void
    {
        // Act
        $response = $this->actingAs($this->user)->getJson('/api/v1/permissions');

        // Assert - ノート関連
        $response->assertOk()
            ->assertJsonFragment([
                'name' => 'view_notes',
                'display_name' => 'ノート閲覧',
            ])
            ->assertJsonFragment([
                'name' => 'create_notes',
                'display_name' => 'ノート作成',
            ])
            ->assertJsonFragment([
                'name' => 'edit_notes',
                'display_name' => 'ノート編集',
            ])
            ->assertJsonFragment([
                'name' => 'delete_notes',
                'display_name' => 'ノート削除',
            ])
            // Assert - チーム関連
            ->assertJsonFragment([
                'name' => 'view_team',
                'display_name' => 'チーム閲覧',
            ])
            ->assertJsonFragment([
                'name' => 'create_team',
                'display_name' => 'チーム作成',
            ])
            // Assert - グループ関連
            ->assertJsonFragment([
                'name' => 'view_group',
                'display_name' => 'グループ閲覧',
            ])
            ->assertJsonFragment([
                'name' => 'create_group',
                'display_name' => 'グループ作成',
            ])
            // Assert - メンバー関連
            ->assertJsonFragment([
                'name' => 'view_members',
                'display_name' => 'メンバー閲覧',
            ])
            ->assertJsonFragment([
                'name' => 'add_members',
                'display_name' => 'メンバー追加',
            ]);
    }

    public function test_it_returns_permissions_with_descriptions(): void
    {
        // Act
        $response = $this->actingAs($this->user)->getJson('/api/v1/permissions');

        // Assert
        $responseData = $response->json();
        $viewNotesPermission = collect($responseData)->firstWhere('name', 'view_notes');

        $this->assertNotNull($viewNotesPermission);
        $this->assertEquals('ノート閲覧', $viewNotesPermission['display_name']);
        $this->assertEquals('ノートを閲覧できる', $viewNotesPermission['description']);
    }

    public function test_it_requires_authentication(): void
    {
        // Act
        $response = $this->getJson('/api/v1/permissions');

        // Assert
        $response->assertUnauthorized();
    }
}
