<?php

declare(strict_types=1);

namespace Tests\Unit\UseCase\Note;

use App\Models\Note;
use App\Models\User;
use App\UseCase\Note\UpdateNoteAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateNoteActionTest extends TestCase
{
    use RefreshDatabase;

    private UpdateNoteAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new UpdateNoteAction();
    }

    public function test_it_updates_note_with_valid_data(): void
    {
        // Arrange
        $user = User::factory()->create();
        $note = Note::factory()->create([
            'user_id' => $user->id,
            'title' => 'Original Title',
            'date' => '2025-01-01',
            'content' => 'Original content',
        ]);
        $updateData = [
            'title' => 'Updated Title',
            'date' => '2025-01-15',
            'content' => 'Updated content',
        ];

        // Act
        $result = $this->action->execute($note, $updateData);

        // Assert
        $this->assertInstanceOf(Note::class, $result);
        $this->assertEquals($note->id, $result->id);
        $this->assertEquals($updateData['title'], $result->title);
        $this->assertEquals($updateData['date'], $result->date->format('Y-m-d'));
        $this->assertEquals($updateData['content'], $result->content);
        $this->assertDatabaseHas('notes', [
            'id' => $note->id,
            'title' => $updateData['title'],
            'content' => $updateData['content'],
        ]);
    }

    public function test_it_updates_only_provided_fields(): void
    {
        // Arrange
        $user = User::factory()->create();
        $note = Note::factory()->create([
            'user_id' => $user->id,
            'title' => 'Original Title',
            'date' => '2025-01-01',
            'content' => 'Original content',
        ]);
        $updateData = [
            'title' => 'Updated Title',
        ];

        // Act
        $result = $this->action->execute($note, $updateData);

        // Assert
        $this->assertEquals($updateData['title'], $result->title);
        $this->assertEquals($note->date, $result->date);
        $this->assertEquals($note->content, $result->content);
    }

    public function test_it_returns_fresh_instance_with_updated_data(): void
    {
        // Arrange
        $user = User::factory()->create();
        $note = Note::factory()->create(['user_id' => $user->id]);
        $oldUpdatedAt = $note->updated_at;
        $updateData = [
            'title' => 'Updated Title',
        ];

        // This is needed to ensure updated_at changes
        $this->travel(1)->seconds();

        // Act
        $result = $this->action->execute($note, $updateData);

        // Assert
        $this->assertNotEquals($oldUpdatedAt, $result->updated_at);
        $this->assertEquals($updateData['title'], $result->fresh()->title);
    }

    public function test_it_preserves_user_id(): void
    {
        // Arrange
        $user = User::factory()->create();
        $note = Note::factory()->create(['user_id' => $user->id]);
        $originalUserId = $note->user_id;
        $updateData = [
            'title' => 'Updated Title',
        ];

        // Act
        $result = $this->action->execute($note, $updateData);

        // Assert
        $this->assertEquals($originalUserId, $result->user_id);
    }
}
