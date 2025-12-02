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
        $response = $this->actingAs($this->user)->getJson('/api/v1/teams/invalid-sqid');

        // Assert
        $response->assertNotFound();
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
        $response = $this->actingAs($this->user)->putJson("/api/v1/teams/{$team->sqid}", $updateData);

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

    public function test_it_deletes_a_team_by_sqid(): void
    {
        // Arrange
        $team = Team::factory()->create();

        // Act
        $response = $this->actingAs($this->user)->deleteJson("/api/v1/teams/{$team->sqid}");

        // Assert
        $response->assertNoContent();
        $this->assertSoftDeleted('teams', ['id' => $team->id]);
    }

    public function test_it_returns_404_when_deleting_non_existent_team(): void
    {
        // Act
        $response = $this->actingAs($this->user)->deleteJson('/api/v1/teams/invalid-sqid');

        // Assert
        $response->assertNotFound();
    }

    public function test_it_adds_a_user_to_team(): void
    {
        // Arrange
        $team = Team::factory()->create();
        $userToAdd = User::factory()->create();
        $data = [
            'email' => $userToAdd->email,
            'is_owner' => false,
        ];

        // Act
        $response = $this->actingAs($this->user)->postJson("/api/v1/teams/{$team->sqid}/users", $data);

        // Assert
        $response->assertCreated()
            ->assertJsonFragment([
                'team_id' => $team->id,
                'user_id' => $userToAdd->id,
                'is_owner' => false,
            ]);

        $this->assertDatabaseHas('team_user', [
            'team_id' => $team->id,
            'user_id' => $userToAdd->id,
            'is_owner' => false,
        ]);
    }

    public function test_it_adds_a_user_to_team_as_owner(): void
    {
        // Arrange
        $team = Team::factory()->create();
        $userToAdd = User::factory()->create();
        $data = [
            'email' => $userToAdd->email,
            'is_owner' => true,
        ];

        // Act
        $response = $this->actingAs($this->user)->postJson("/api/v1/teams/{$team->sqid}/users", $data);

        // Assert
        $response->assertCreated()
            ->assertJsonFragment([
                'team_id' => $team->id,
                'user_id' => $userToAdd->id,
                'is_owner' => true,
            ]);

        $this->assertDatabaseHas('team_user', [
            'team_id' => $team->id,
            'user_id' => $userToAdd->id,
            'is_owner' => true,
        ]);
    }

    public function test_it_returns_422_when_user_already_exists_in_team(): void
    {
        // Arrange
        $team = Team::factory()->create();
        $userToAdd = User::factory()->create();

        // 事前にユーザーをチームに登録
        $team->users()->attach($userToAdd->id, ['is_owner' => false]);

        $data = [
            'email' => $userToAdd->email,
            'is_owner' => false,
        ];

        // Act
        $response = $this->actingAs($this->user)->postJson("/api/v1/teams/{$team->sqid}/users", $data);

        // Assert
        $response->assertUnprocessable()
            ->assertJsonFragment([
                'message' => 'ユーザーが見つからないか、すでにチームに登録されています',
            ]);
    }

    public function test_it_returns_404_when_adding_user_to_non_existent_team(): void
    {
        // Arrange
        $userToAdd = User::factory()->create();
        $data = [
            'email' => $userToAdd->email,
            'is_owner' => false,
        ];

        // Act
        $response = $this->actingAs($this->user)->postJson('/api/v1/teams/invalid-sqid/users', $data);

        // Assert
        $response->assertNotFound();
    }

    public function test_it_validates_required_fields_when_adding_user(): void
    {
        // Arrange
        $team = Team::factory()->create();

        // Act
        $response = $this->actingAs($this->user)->postJson("/api/v1/teams/{$team->sqid}/users", []);

        // Assert
        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    public function test_it_validates_user_exists_when_adding_user(): void
    {
        // Arrange
        $team = Team::factory()->create();
        $data = [
            'email' => 'nonexistent@example.com', // 存在しないメールアドレス
            'is_owner' => false,
        ];

        // Act
        $response = $this->actingAs($this->user)->postJson("/api/v1/teams/{$team->sqid}/users", $data);

        // Assert
        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    public function test_it_requires_authentication_for_adding_user(): void
    {
        // Arrange
        $team = Team::factory()->create();
        $userToAdd = User::factory()->create();
        $data = [
            'email' => $userToAdd->email,
            'is_owner' => false,
        ];

        // Act
        $response = $this->postJson("/api/v1/teams/{$team->sqid}/users", $data);

        // Assert
        $response->assertUnauthorized();
    }

    public function test_it_returns_team_users(): void
    {
        // Arrange
        $team = Team::factory()->create();
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $team->users()->attach($user1->id, ['is_owner' => true]);
        $team->users()->attach($user2->id, ['is_owner' => false]);

        // Act
        $response = $this->actingAs($this->user)->getJson("/api/v1/teams/{$team->sqid}/users");

        // Assert
        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'sqid', 'name', 'email', 'is_owner', 'created_at'],
                ],
            ])
            ->assertJsonCount(2, 'data');
    }

    public function test_it_returns_empty_array_when_no_users_in_team(): void
    {
        // Arrange
        $team = Team::factory()->create();

        // Act
        $response = $this->actingAs($this->user)->getJson("/api/v1/teams/{$team->sqid}/users");

        // Assert
        $response->assertOk()
            ->assertJsonCount(0, 'data');
    }

    public function test_it_returns_404_when_getting_users_for_non_existent_team(): void
    {
        // Act
        $response = $this->actingAs($this->user)->getJson('/api/v1/teams/invalid-sqid/users');

        // Assert
        $response->assertNotFound();
    }

    public function test_it_requires_authentication_for_getting_users(): void
    {
        // Arrange
        $team = Team::factory()->create();

        // Act
        $response = $this->getJson("/api/v1/teams/{$team->sqid}/users");

        // Assert
        $response->assertUnauthorized();
    }

    public function test_it_updates_team_user(): void
    {
        // Arrange
        $team = Team::factory()->create();
        $userToUpdate = User::factory()->create();
        $team->users()->attach($userToUpdate->id, ['is_owner' => false]);

        $data = [
            'is_owner' => true,
        ];

        // Act
        $response = $this->actingAs($this->user)->putJson("/api/v1/teams/{$team->sqid}/users/{$userToUpdate->id}", $data);

        // Assert
        $response->assertOk()
            ->assertJsonFragment([
                'team_id' => $team->id,
                'user_id' => $userToUpdate->id,
                'is_owner' => true,
            ]);

        $this->assertDatabaseHas('team_user', [
            'team_id' => $team->id,
            'user_id' => $userToUpdate->id,
            'is_owner' => true,
        ]);
    }

    public function test_it_returns_404_when_updating_non_existent_user_in_team(): void
    {
        // Arrange
        $team = Team::factory()->create();
        $userNotInTeam = User::factory()->create();

        $data = [
            'is_owner' => true,
        ];

        // Act
        $response = $this->actingAs($this->user)->putJson("/api/v1/teams/{$team->sqid}/users/{$userNotInTeam->id}", $data);

        // Assert
        $response->assertNotFound()
            ->assertJsonFragment([
                'message' => 'ユーザーがチームに登録されていません',
            ]);
    }

    public function test_it_validates_required_fields_when_updating_user(): void
    {
        // Arrange
        $team = Team::factory()->create();
        $userToUpdate = User::factory()->create();
        $team->users()->attach($userToUpdate->id, ['is_owner' => false]);

        // Act
        $response = $this->actingAs($this->user)->putJson("/api/v1/teams/{$team->sqid}/users/{$userToUpdate->id}", []);

        // Assert
        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['is_owner']);
    }

    public function test_it_requires_authentication_for_updating_user(): void
    {
        // Arrange
        $team = Team::factory()->create();
        $userToUpdate = User::factory()->create();
        $team->users()->attach($userToUpdate->id, ['is_owner' => false]);

        $data = [
            'is_owner' => true,
        ];

        // Act
        $response = $this->putJson("/api/v1/teams/{$team->sqid}/users/{$userToUpdate->id}", $data);

        // Assert
        $response->assertUnauthorized();
    }

    public function test_it_removes_user_from_team(): void
    {
        // Arrange
        $team = Team::factory()->create();
        $userToRemove = User::factory()->create();
        $team->users()->attach($userToRemove->id, ['is_owner' => false]);

        // Act
        $response = $this->actingAs($this->user)->deleteJson("/api/v1/teams/{$team->sqid}/users/{$userToRemove->id}");

        // Assert
        $response->assertNoContent();
        $this->assertDatabaseMissing('team_user', [
            'team_id' => $team->id,
            'user_id' => $userToRemove->id,
        ]);
    }

    public function test_it_returns_404_when_removing_non_existent_user_from_team(): void
    {
        // Arrange
        $team = Team::factory()->create();
        $userNotInTeam = User::factory()->create();

        // Act
        $response = $this->actingAs($this->user)->deleteJson("/api/v1/teams/{$team->sqid}/users/{$userNotInTeam->id}");

        // Assert
        $response->assertNotFound()
            ->assertJsonFragment([
                'message' => 'ユーザーがチームに登録されていません',
            ]);
    }

    public function test_it_requires_authentication_for_removing_user(): void
    {
        // Arrange
        $team = Team::factory()->create();
        $userToRemove = User::factory()->create();
        $team->users()->attach($userToRemove->id, ['is_owner' => false]);

        // Act
        $response = $this->deleteJson("/api/v1/teams/{$team->sqid}/users/{$userToRemove->id}");

        // Assert
        $response->assertUnauthorized();
    }
}
