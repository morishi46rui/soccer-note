<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Api\V1;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();

        // シーダーを実行してロールと権限を作成
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    public function test_it_returns_all_roles_with_permissions(): void
    {
        // Act
        $response = $this->actingAs($this->user)->getJson('/api/v1/roles');

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
                    'permissions' => [
                        '*' => [
                            'id',
                            'name',
                            'display_name',
                            'description',
                            'created_at',
                            'updated_at',
                        ],
                    ],
                ],
            ])
            ->assertJsonCount(4); // player, coach, manager, admin
    }

    public function test_it_returns_correct_role_data(): void
    {
        // Act
        $response = $this->actingAs($this->user)->getJson('/api/v1/roles');

        // Assert
        $response->assertOk()
            ->assertJsonFragment([
                'name' => 'player',
                'display_name' => '選手',
            ])
            ->assertJsonFragment([
                'name' => 'coach',
                'display_name' => 'コーチ',
            ])
            ->assertJsonFragment([
                'name' => 'manager',
                'display_name' => 'マネージャー',
            ])
            ->assertJsonFragment([
                'name' => 'admin',
                'display_name' => '管理者',
            ]);
    }

    public function test_it_returns_player_role_with_correct_permissions(): void
    {
        // Act
        $response = $this->actingAs($this->user)->getJson('/api/v1/roles');

        // Assert
        $responseData = $response->json();
        $playerRole = collect($responseData)->firstWhere('name', 'player');

        $this->assertNotNull($playerRole);
        $this->assertCount(1, $playerRole['permissions']);
        $this->assertEquals('view_notes', $playerRole['permissions'][0]['name']);
    }

    public function test_it_returns_coach_role_with_correct_permissions(): void
    {
        // Act
        $response = $this->actingAs($this->user)->getJson('/api/v1/roles');

        // Assert
        $responseData = $response->json();
        $coachRole = collect($responseData)->firstWhere('name', 'coach');

        $this->assertNotNull($coachRole);
        $this->assertCount(3, $coachRole['permissions']);

        $permissionNames = collect($coachRole['permissions'])->pluck('name')->toArray();
        $this->assertContains('view_notes', $permissionNames);
        $this->assertContains('edit_notes', $permissionNames);
        $this->assertContains('delete_notes', $permissionNames);
    }

    public function test_it_returns_admin_role_with_all_permissions(): void
    {
        // Act
        $response = $this->actingAs($this->user)->getJson('/api/v1/roles');

        // Assert
        $responseData = $response->json();
        $adminRole = collect($responseData)->firstWhere('name', 'admin');

        $this->assertNotNull($adminRole);
        $this->assertCount(6, $adminRole['permissions']); // すべての権限
    }

    public function test_it_requires_authentication(): void
    {
        // Act
        $response = $this->getJson('/api/v1/roles');

        // Assert
        $response->assertUnauthorized();
    }
}
