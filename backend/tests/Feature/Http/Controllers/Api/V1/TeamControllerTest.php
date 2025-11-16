<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Api\V1;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_it_returns_team_list(): void
    {
        // Arrange
        Team::factory()->count(3)->create();

        // Act
        $response = $this->actingAs($this->user)->getJson('/api/v1/teams');

        // Assert
        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'sqid', 'name', 'description', 'created_at', 'updated_at'],
                ],
                'current_page',
                'last_page',
                'per_page',
                'total',
            ])
            ->assertJsonCount(3, 'data');
    }

    public function test_it_returns_paginated_teams(): void
    {
        // Arrange
        Team::factory()->count(20)->create();

        // Act
        $response = $this->actingAs($this->user)->getJson('/api/v1/teams?per_page=5&page=2');

        // Assert
        $response->assertOk()
            ->assertJsonPath('current_page', 2)
            ->assertJsonPath('per_page', 5)
            ->assertJsonCount(5, 'data');
    }

    public function test_it_requires_authentication_for_team_list(): void
    {
        // Act
        $response = $this->getJson('/api/v1/teams');

        // Assert
        $response->assertUnauthorized();
    }

    public function test_it_creates_a_team(): void
    {
        // Arrange
        $data = [
            'name' => 'FC東京',
            'description' => '東京のサッカーチーム',
        ];

        // Act
        $response = $this->actingAs($this->user)->postJson('/api/v1/teams', $data);

        // Assert
        $response->assertCreated()
            ->assertJsonFragment([
                'name' => 'FC東京',
                'description' => '東京のサッカーチーム',
            ]);

        $this->assertDatabaseHas('teams', [
            'name' => 'FC東京',
            'description' => '東京のサッカーチーム',
        ]);
    }

    public function test_it_creates_a_team_without_description(): void
    {
        // Arrange
        $data = [
            'name' => 'FC大阪',
        ];

        // Act
        $response = $this->actingAs($this->user)->postJson('/api/v1/teams', $data);

        // Assert
        $response->assertCreated()
            ->assertJsonFragment([
                'name' => 'FC大阪',
                'description' => null,
            ]);

        $this->assertDatabaseHas('teams', [
            'name' => 'FC大阪',
            'description' => null,
        ]);
    }

    public function test_it_validates_required_fields_when_creating_team(): void
    {
        // Act
        $response = $this->actingAs($this->user)->postJson('/api/v1/teams', []);

        // Assert
        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    }

    public function test_it_validates_max_length_when_creating_team(): void
    {
        // Arrange
        $data = [
            'name' => str_repeat('あ', 256), // 256文字
        ];

        // Act
        $response = $this->actingAs($this->user)->postJson('/api/v1/teams', $data);

        // Assert
        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    }

    public function test_it_returns_a_single_team(): void
    {
        // Arrange
        $team = Team::factory()->create();

        // Act
        $response = $this->actingAs($this->user)->getJson("/api/v1/teams/{$team->id}");

        // Assert
        $response->assertOk()
            ->assertJsonFragment([
                'id' => $team->id,
                'name' => $team->name,
                'description' => $team->description,
            ]);
    }

    public function test_it_returns_a_single_team_by_sqid(): void
    {
        // Arrange
        $team = Team::factory()->create();

        // Act
        $response = $this->actingAs($this->user)->getJson("/api/v1/teams/{$team->sqid}");

        // Assert
        $response->assertOk()
            ->assertJsonFragment([
                'id' => $team->id,
                'name' => $team->name,
                'description' => $team->description,
            ]);
    }

    public function test_it_returns_404_when_team_not_found(): void
    {
        // Act
        $response = $this->actingAs($this->user)->getJson('/api/v1/teams/99999');

        // Assert
        $response->assertNotFound();
    }

    public function test_it_updates_a_team(): void
    {
        // Arrange
        $team = Team::factory()->create();
        $updateData = [
            'name' => '更新されたチーム名',
            'description' => '更新された説明',
        ];

        // Act
        $response = $this->actingAs($this->user)->putJson("/api/v1/teams/{$team->id}", $updateData);

        // Assert
        $response->assertOk()
            ->assertJsonFragment([
                'id' => $team->id,
                'name' => '更新されたチーム名',
                'description' => '更新された説明',
            ]);

        $this->assertDatabaseHas('teams', [
            'id' => $team->id,
            'name' => '更新されたチーム名',
            'description' => '更新された説明',
        ]);
    }

    public function test_it_updates_a_team_by_sqid(): void
    {
        // Arrange
        $team = Team::factory()->create();
        $updateData = [
            'name' => '更新されたチーム名',
            'description' => '更新された説明',
        ];

        // Act
        $response = $this->actingAs($this->user)->putJson("/api/v1/teams/{$team->sqid}", $updateData);

        // Assert
        $response->assertOk()
            ->assertJsonFragment([
                'id' => $team->id,
                'name' => '更新されたチーム名',
                'description' => '更新された説明',
            ]);

        $this->assertDatabaseHas('teams', [
            'id' => $team->id,
            'name' => '更新されたチーム名',
            'description' => '更新された説明',
        ]);
    }

    public function test_it_partially_updates_a_team(): void
    {
        // Arrange
        $team = Team::factory()->create(['name' => '元のチーム名', 'description' => '元の説明']);
        $updateData = [
            'name' => '新しいチーム名',
        ];

        // Act
        $response = $this->actingAs($this->user)->putJson("/api/v1/teams/{$team->id}", $updateData);

        // Assert
        $response->assertOk()
            ->assertJsonFragment([
                'id' => $team->id,
                'name' => '新しいチーム名',
                'description' => '元の説明',
            ]);

        $this->assertDatabaseHas('teams', [
            'id' => $team->id,
            'name' => '新しいチーム名',
            'description' => '元の説明',
        ]);
    }

    public function test_it_deletes_a_team(): void
    {
        // Arrange
        $team = Team::factory()->create();

        // Act
        $response = $this->actingAs($this->user)->deleteJson("/api/v1/teams/{$team->id}");

        // Assert
        $response->assertNoContent();
        $this->assertDatabaseMissing('teams', ['id' => $team->id]);
    }

    public function test_it_deletes_a_team_by_sqid(): void
    {
        // Arrange
        $team = Team::factory()->create();

        // Act
        $response = $this->actingAs($this->user)->deleteJson("/api/v1/teams/{$team->sqid}");

        // Assert
        $response->assertNoContent();
        $this->assertDatabaseMissing('teams', ['id' => $team->id]);
    }

    public function test_it_returns_404_when_deleting_non_existent_team(): void
    {
        // Act
        $response = $this->actingAs($this->user)->deleteJson('/api/v1/teams/99999');

        // Assert
        $response->assertNotFound();
    }
}
