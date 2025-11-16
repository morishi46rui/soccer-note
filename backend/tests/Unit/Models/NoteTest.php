<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Note;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NoteTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_has_fillable_attributes(): void
    {
        // Arrange
        $attributes = [
            'user_id',
            'title',
            'date',
            'content',
        ];

        // Act
        $note = new Note;

        // Assert
        $this->assertEquals($attributes, $note->getFillable());
    }

    public function test_it_casts_attributes_correctly(): void
    {
        // Arrange
        $expectedCasts = [
            'id' => 'int',
            'date' => 'date:Y-m-d',
            'deleted_at' => 'datetime',
        ];

        // Act
        $note = new Note;

        // Assert
        foreach ($expectedCasts as $attribute => $cast) {
            $this->assertEquals($cast, $note->getCasts()[$attribute]);
        }
    }

    public function test_it_belongs_to_user(): void
    {
        // Arrange
        $user = User::factory()->create();
        $note = Note::factory()->create(['user_id' => $user->id]);

        // Act
        $relationship = $note->user;

        // Assert
        $this->assertInstanceOf(User::class, $relationship);
        $this->assertEquals($user->id, $relationship->id);
    }

    public function test_it_can_be_soft_deleted(): void
    {
        // Arrange
        $note = Note::factory()->create();

        // Act
        $note->delete();

        // Assert
        $this->assertSoftDeleted('notes', ['id' => $note->id]);
        $this->assertNotNull($note->fresh()->deleted_at);
    }

    public function test_it_can_be_restored_after_soft_delete(): void
    {
        // Arrange
        $note = Note::factory()->create();
        $note->delete();

        // Act
        $note->restore();

        // Assert
        $this->assertDatabaseHas('notes', [
            'id' => $note->id,
            'deleted_at' => null,
        ]);
    }

    public function test_it_can_be_force_deleted(): void
    {
        // Arrange
        $note = Note::factory()->create();

        // Act
        $note->forceDelete();

        // Assert
        $this->assertDatabaseMissing('notes', ['id' => $note->id]);
    }

    public function test_it_excludes_soft_deleted_notes_by_default(): void
    {
        // Arrange
        $activeNote = Note::factory()->create();
        $deletedNote = Note::factory()->create();
        $deletedNote->delete();

        // Act
        $notes = Note::all();

        // Assert
        $this->assertCount(1, $notes);
        $this->assertTrue($notes->contains($activeNote));
        $this->assertFalse($notes->contains($deletedNote));
    }

    public function test_it_includes_soft_deleted_notes_with_trashed(): void
    {
        // Arrange
        $activeNote = Note::factory()->create();
        $deletedNote = Note::factory()->create();
        $deletedNote->delete();

        // Act
        $notes = Note::withTrashed()->get();

        // Assert
        $this->assertCount(2, $notes);
        $this->assertTrue($notes->contains($activeNote));
        $this->assertTrue($notes->contains($deletedNote));
    }
}
