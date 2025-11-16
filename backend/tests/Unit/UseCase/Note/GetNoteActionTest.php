<?php

declare(strict_types=1);

namespace Tests\Unit\UseCase\Note;

use App\Models\Note;
use App\Models\User;
use App\UseCase\Note\GetNoteAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetNoteActionTest extends TestCase
{
    use RefreshDatabase;

    private GetNoteAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new GetNoteAction;
    }

    public function test_it_returns_note_for_authorized_user(): void
    {
        // Arrange
        $user = User::factory()->create();
        $note = Note::factory()->create(['user_id' => $user->id]);

        // Act
        $result = $this->action->execute($note->id, $user->id);

        // Assert
        $this->assertInstanceOf(Note::class, $result);
        $this->assertEquals($note->id, $result->id);
        $this->assertEquals($note->title, $result->title);
    }

    public function test_it_returns_null_for_unauthorized_user(): void
    {
        // Arrange
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $note = Note::factory()->create(['user_id' => $owner->id]);

        // Act
        $result = $this->action->execute($note->id, $otherUser->id);

        // Assert
        $this->assertNull($result);
    }

    public function test_it_returns_null_for_nonexistent_note(): void
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $result = $this->action->execute(999999, $user->id);

        // Assert
        $this->assertNull($result);
    }

    public function test_it_returns_null_for_soft_deleted_note(): void
    {
        // Arrange
        $user = User::factory()->create();
        $note = Note::factory()->create(['user_id' => $user->id]);
        $note->delete();

        // Act
        $result = $this->action->execute($note->id, $user->id);

        // Assert
        $this->assertNull($result);
    }
}
