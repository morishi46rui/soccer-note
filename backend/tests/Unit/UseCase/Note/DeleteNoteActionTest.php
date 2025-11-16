<?php

declare(strict_types=1);

namespace Tests\Unit\UseCase\Note;

use App\Models\Note;
use App\Models\User;
use App\UseCase\Note\DeleteNoteAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteNoteActionTest extends TestCase
{
    use RefreshDatabase;

    private DeleteNoteAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new DeleteNoteAction;
    }

    public function test_it_soft_deletes_note(): void
    {
        // Arrange
        $user = User::factory()->create();
        $note = Note::factory()->create(['user_id' => $user->id]);

        // Act
        $result = $this->action->execute($note);

        // Assert
        $this->assertTrue($result);
        $this->assertSoftDeleted('notes', ['id' => $note->id]);
        $this->assertNotNull($note->fresh()->deleted_at);
    }

    public function test_it_returns_true_on_successful_deletion(): void
    {
        // Arrange
        $user = User::factory()->create();
        $note = Note::factory()->create(['user_id' => $user->id]);

        // Act
        $result = $this->action->execute($note);

        // Assert
        $this->assertTrue($result);
    }

    public function test_it_keeps_note_in_database_after_soft_delete(): void
    {
        // Arrange
        $user = User::factory()->create();
        $note = Note::factory()->create(['user_id' => $user->id]);
        $noteId = $note->id;

        // Act
        $this->action->execute($note);

        // Assert
        $this->assertDatabaseHas('notes', ['id' => $noteId]);
        $this->assertNotNull(Note::withTrashed()->find($noteId));
    }

    public function test_it_hides_note_from_default_queries(): void
    {
        // Arrange
        $user = User::factory()->create();
        $note = Note::factory()->create(['user_id' => $user->id]);
        $noteId = $note->id;

        // Act
        $this->action->execute($note);

        // Assert
        $this->assertNull(Note::find($noteId));
        $this->assertNotNull(Note::withTrashed()->find($noteId));
    }

    public function test_it_sets_deleted_at_timestamp(): void
    {
        // Arrange
        $user = User::factory()->create();
        $note = Note::factory()->create(['user_id' => $user->id]);
        $this->assertNull($note->deleted_at);

        // Act
        $this->action->execute($note);

        // Assert
        $freshNote = Note::withTrashed()->find($note->id);
        $this->assertNotNull($freshNote->deleted_at);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $freshNote->deleted_at);
    }
}
