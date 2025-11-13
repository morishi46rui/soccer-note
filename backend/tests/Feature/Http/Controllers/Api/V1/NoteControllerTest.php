<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Api\V1;

use App\Models\Note;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NoteControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_it_returns_note_list(): void
    {
        // Arrange
        Note::factory()->count(3)->create(['user_id' => $this->user->id]);
        Note::factory()->create(); // Other user's note

        // Act
        $response = $this->actingAs($this->user)->getJson('/api/v1/notes');

        // Assert
        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'user_id', 'title', 'date', 'content', 'created_at', 'updated_at'],
                ],
                'current_page',
                'last_page',
                'per_page',
                'total',
            ])
            ->assertJsonCount(3, 'data');
    }

    public function test_it_returns_paginated_notes(): void
    {
        // Arrange
        Note::factory()->count(20)->create(['user_id' => $this->user->id]);

        // Act
        $response = $this->actingAs($this->user)->getJson('/api/v1/notes?per_page=5&page=2');

        // Assert
        $response->assertOk()
            ->assertJsonPath('current_page', 2)
            ->assertJsonPath('per_page', 5)
            ->assertJsonCount(5, 'data');
    }

    public function test_it_requires_authentication_for_note_list(): void
    {
        // Act
        $response = $this->getJson('/api/v1/notes');

        // Assert
        $response->assertUnauthorized();
    }

    public function test_it_creates_a_note(): void
    {
        // Arrange
        $data = [
            'title' => '練習試合の振り返り',
            'date' => '2025-11-13',
            'content' => '今日の練習では...',
        ];

        // Act
        $response = $this->actingAs($this->user)->postJson('/api/v1/notes', $data);

        // Assert
        $response->assertCreated()
            ->assertJsonFragment([
                'title' => '練習試合の振り返り',
                'date' => '2025-11-13',
                'content' => '今日の練習では...',
                'user_id' => $this->user->id,
            ]);

        $this->assertDatabaseHas('notes', [
            'user_id' => $this->user->id,
            'title' => '練習試合の振り返り',
        ]);
    }

    public function test_it_validates_required_fields_when_creating_note(): void
    {
        // Act
        $response = $this->actingAs($this->user)->postJson('/api/v1/notes', []);

        // Assert
        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['title', 'date', 'content']);
    }

    public function test_it_validates_date_format_when_creating_note(): void
    {
        // Arrange
        $data = [
            'title' => 'テスト',
            'date' => 'invalid-date',
            'content' => 'テスト内容',
        ];

        // Act
        $response = $this->actingAs($this->user)->postJson('/api/v1/notes', $data);

        // Assert
        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['date']);
    }

    public function test_it_returns_a_single_note(): void
    {
        // Arrange
        $note = Note::factory()->create(['user_id' => $this->user->id]);

        // Act
        $response = $this->actingAs($this->user)->getJson("/api/v1/notes/{$note->id}");

        // Assert
        $response->assertOk()
            ->assertJsonFragment([
                'id' => $note->id,
                'title' => $note->title,
                'user_id' => $this->user->id,
            ]);
    }

    public function test_it_returns_a_single_note_by_sqid(): void
    {
        // Arrange
        $note = Note::factory()->create(['user_id' => $this->user->id]);

        // Act
        $response = $this->actingAs($this->user)->getJson("/api/v1/notes/{$note->sqid}");

        // Assert
        $response->assertOk()
            ->assertJsonFragment([
                'id' => $note->id,
                'title' => $note->title,
                'user_id' => $this->user->id,
            ]);
    }

    public function test_it_returns_404_when_note_not_found(): void
    {
        // Act
        $response = $this->actingAs($this->user)->getJson('/api/v1/notes/99999');

        // Assert
        $response->assertNotFound();
    }

    public function test_it_prevents_access_to_other_users_note(): void
    {
        // Arrange
        $otherUser = User::factory()->create();
        $note = Note::factory()->create(['user_id' => $otherUser->id]);

        // Act
        $response = $this->actingAs($this->user)->getJson("/api/v1/notes/{$note->id}");

        // Assert
        $response->assertNotFound();
    }

    public function test_it_updates_a_note(): void
    {
        // Arrange
        $note = Note::factory()->create(['user_id' => $this->user->id]);
        $updateData = [
            'title' => '更新されたタイトル',
            'date' => '2025-11-14',
            'content' => '更新された内容',
        ];

        // Act
        $response = $this->actingAs($this->user)->putJson("/api/v1/notes/{$note->id}", $updateData);

        // Assert
        $response->assertOk()
            ->assertJsonFragment([
                'id' => $note->id,
                'title' => '更新されたタイトル',
                'date' => '2025-11-14',
                'content' => '更新された内容',
            ]);

        $this->assertDatabaseHas('notes', [
            'id' => $note->id,
            'title' => '更新されたタイトル',
        ]);
    }

    public function test_it_updates_a_note_by_sqid(): void
    {
        // Arrange
        $note = Note::factory()->create(['user_id' => $this->user->id]);
        $updateData = [
            'title' => '更新されたタイトル',
            'date' => '2025-11-14',
            'content' => '更新された内容',
        ];

        // Act
        $response = $this->actingAs($this->user)->putJson("/api/v1/notes/{$note->sqid}", $updateData);

        // Assert
        $response->assertOk()
            ->assertJsonFragment([
                'id' => $note->id,
                'title' => '更新されたタイトル',
                'date' => '2025-11-14',
                'content' => '更新された内容',
            ]);

        $this->assertDatabaseHas('notes', [
            'id' => $note->id,
            'title' => '更新されたタイトル',
        ]);
    }

    public function test_it_validates_required_fields_when_updating_note(): void
    {
        // Arrange
        $note = Note::factory()->create(['user_id' => $this->user->id]);

        // Act
        $response = $this->actingAs($this->user)->putJson("/api/v1/notes/{$note->id}", []);

        // Assert
        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['title', 'date', 'content']);
    }

    public function test_it_prevents_updating_other_users_note(): void
    {
        // Arrange
        $otherUser = User::factory()->create();
        $note = Note::factory()->create(['user_id' => $otherUser->id]);
        $updateData = [
            'title' => '不正な更新',
            'date' => '2025-11-14',
            'content' => '不正な内容',
        ];

        // Act
        $response = $this->actingAs($this->user)->putJson("/api/v1/notes/{$note->id}", $updateData);

        // Assert
        $response->assertNotFound();
        $this->assertDatabaseMissing('notes', ['title' => '不正な更新']);
    }

    public function test_it_deletes_a_note(): void
    {
        // Arrange
        $note = Note::factory()->create(['user_id' => $this->user->id]);

        // Act
        $response = $this->actingAs($this->user)->deleteJson("/api/v1/notes/{$note->id}");

        // Assert
        $response->assertNoContent();
        $this->assertSoftDeleted('notes', ['id' => $note->id]);
    }

    public function test_it_deletes_a_note_by_sqid(): void
    {
        // Arrange
        $note = Note::factory()->create(['user_id' => $this->user->id]);

        // Act
        $response = $this->actingAs($this->user)->deleteJson("/api/v1/notes/{$note->sqid}");

        // Assert
        $response->assertNoContent();
        $this->assertSoftDeleted('notes', ['id' => $note->id]);
    }

    public function test_it_prevents_deleting_other_users_note(): void
    {
        // Arrange
        $otherUser = User::factory()->create();
        $note = Note::factory()->create(['user_id' => $otherUser->id]);

        // Act
        $response = $this->actingAs($this->user)->deleteJson("/api/v1/notes/{$note->id}");

        // Assert
        $response->assertNotFound();
        $this->assertDatabaseHas('notes', [
            'id' => $note->id,
            'deleted_at' => null,
        ]);
    }
}
