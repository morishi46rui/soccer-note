<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Api\V1;

use App\Models\Group;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GroupControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_it_returns_group_list(): void
    {
        // Arrange
        Group::factory()->count(3)->create();

        // Act
        $response = $this->actingAs($this->user)->getJson('/api/v1/groups');

        // Assert
        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'sqid', 'team_id', 'name', 'description', 'created_at', 'updated_at'],
                ],
                'current_page',
                'last_page',
                'per_page',
                'total',
            ])
            ->assertJsonCount(3, 'data');
    }

    public function test_it_returns_paginated_groups(): void
    {
        // Arrange
        Group::factory()->count(20)->create();

        // Act
        $response = $this->actingAs($this->user)->getJson('/api/v1/groups?per_page=5&page=2');

        // Assert
        $response->assertOk()
            ->assertJsonPath('current_page', 2)
            ->assertJsonPath('per_page', 5)
            ->assertJsonCount(5, 'data');
    }

    public function test_it_requires_authentication_for_group_list(): void
    {
        // Act
        $response = $this->getJson('/api/v1/groups');

        // Assert
        $response->assertUnauthorized();
    }

    public function test_it_creates_a_group(): void
    {
        // Arrange
        $team = Team::factory()->create();
        $data = [
            'team_id' => $team->id,
            'name' => 'Aグループ',
            'description' => 'グループAの説明',
        ];

        // Act
        $response = $this->actingAs($this->user)->postJson('/api/v1/groups', $data);

        // Assert
        $response->assertCreated()
            ->assertJsonFragment([
                'team_id' => $team->id,
                'name' => 'Aグループ',
                'description' => 'グループAの説明',
            ]);

        $this->assertDatabaseHas('groups', [
            'team_id' => $team->id,
            'name' => 'Aグループ',
            'description' => 'グループAの説明',
        ]);
    }

    public function test_it_creates_a_group_without_description(): void
    {
        // Arrange
        $team = Team::factory()->create();
        $data = [
            'team_id' => $team->id,
            'name' => 'Bグループ',
        ];

        // Act
        $response = $this->actingAs($this->user)->postJson('/api/v1/groups', $data);

        // Assert
        $response->assertCreated()
            ->assertJsonFragment([
                'team_id' => $team->id,
                'name' => 'Bグループ',
                'description' => null,
            ]);

        $this->assertDatabaseHas('groups', [
            'team_id' => $team->id,
            'name' => 'Bグループ',
            'description' => null,
        ]);
    }

    public function test_it_validates_required_fields_when_creating_group(): void
    {
        // Act
        $response = $this->actingAs($this->user)->postJson('/api/v1/groups', []);

        // Assert
        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['team_id', 'name']);
    }

    public function test_it_validates_team_id_exists_when_creating_group(): void
    {
        // Arrange
        $data = [
            'team_id' => 999,
            'name' => 'Cグループ',
        ];

        // Act
        $response = $this->actingAs($this->user)->postJson('/api/v1/groups', $data);

        // Assert
        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['team_id']);
    }

    public function test_it_validates_max_length_when_creating_group(): void
    {
        // Arrange
        $team = Team::factory()->create();
        $data = [
            'team_id' => $team->id,
            'name' => str_repeat('あ', 256), // 256文字
        ];

        // Act
        $response = $this->actingAs($this->user)->postJson('/api/v1/groups', $data);

        // Assert
        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    }

    public function test_it_returns_a_single_group_by_sqid(): void
    {
        // Arrange
        $group = Group::factory()->create();

        // Act
        $response = $this->actingAs($this->user)->getJson("/api/v1/groups/{$group->sqid}");

        // Assert
        $response->assertOk()
            ->assertJsonFragment([
                'id' => $group->id,
                'team_id' => $group->team_id,
                'name' => $group->name,
                'description' => $group->description,
            ]);
    }

    public function test_it_returns_404_when_group_not_found(): void
    {
        // Act
        $response = $this->actingAs($this->user)->getJson('/api/v1/groups/invalid-sqid');

        // Assert
        $response->assertNotFound();
    }

    public function test_it_updates_a_group_by_sqid(): void
    {
        // Arrange
        $group = Group::factory()->create();
        $updateData = [
            'name' => '更新されたグループ名',
            'description' => '更新された説明',
        ];

        // Act
        $response = $this->actingAs($this->user)->putJson("/api/v1/groups/{$group->sqid}", $updateData);

        // Assert
        $response->assertOk()
            ->assertJsonFragment([
                'id' => $group->id,
                'name' => '更新されたグループ名',
                'description' => '更新された説明',
            ]);

        $this->assertDatabaseHas('groups', [
            'id' => $group->id,
            'name' => '更新されたグループ名',
            'description' => '更新された説明',
        ]);
    }

    public function test_it_partially_updates_a_group(): void
    {
        // Arrange
        $group = Group::factory()->create(['name' => '元のグループ名', 'description' => '元の説明']);
        $updateData = [
            'name' => '新しいグループ名',
        ];

        // Act
        $response = $this->actingAs($this->user)->putJson("/api/v1/groups/{$group->sqid}", $updateData);

        // Assert
        $response->assertOk()
            ->assertJsonFragment([
                'id' => $group->id,
                'name' => '新しいグループ名',
                'description' => '元の説明',
            ]);

        $this->assertDatabaseHas('groups', [
            'id' => $group->id,
            'name' => '新しいグループ名',
            'description' => '元の説明',
        ]);
    }

    public function test_it_deletes_a_group_by_sqid(): void
    {
        // Arrange
        $group = Group::factory()->create();

        // Act
        $response = $this->actingAs($this->user)->deleteJson("/api/v1/groups/{$group->sqid}");

        // Assert
        $response->assertNoContent();
        $this->assertDatabaseMissing('groups', ['id' => $group->id]);
    }

    public function test_it_returns_404_when_deleting_non_existent_group(): void
    {
        // Act
        $response = $this->actingAs($this->user)->deleteJson('/api/v1/groups/invalid-sqid');

        // Assert
        $response->assertNotFound();
    }
}
