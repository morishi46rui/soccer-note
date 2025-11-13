<?php

declare(strict_types=1);

namespace Tests\Unit\UseCase\Note;

use App\Models\Note;
use App\Models\User;
use App\UseCase\Note\CreateNoteAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateNoteActionTest extends TestCase
{
    use RefreshDatabase;

    private CreateNoteAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new CreateNoteAction();
    }

    public function test_it_creates_note_with_valid_data(): void
    {
        // Arrange
        $user = User::factory()->create();
        $data = [
            'title' => 'Test Note',
            'date' => '2025-01-15',
            'content' => 'This is a test note content.',
        ];

        // Act
        $result = $this->action->execute($user->id, $data);

        // Assert
        $this->assertInstanceOf(Note::class, $result);
        $this->assertEquals($user->id, $result->user_id);
        $this->assertEquals($data['title'], $result->title);
        $this->assertEquals($data['date'], $result->date->format('Y-m-d'));
        $this->assertEquals($data['content'], $result->content);
        $this->assertDatabaseHas('notes', [
            'user_id' => $user->id,
            'title' => $data['title'],
            'content' => $data['content'],
        ]);
    }

    public function test_it_assigns_correct_user_id(): void
    {
        // Arrange
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $data = [
            'title' => 'User 1 Note',
            'date' => '2025-01-15',
            'content' => 'Content for user 1',
        ];

        // Act
        $result = $this->action->execute($user1->id, $data);

        // Assert
        $this->assertEquals($user1->id, $result->user_id);
        $this->assertNotEquals($user2->id, $result->user_id);
    }

    public function test_it_returns_note_with_all_attributes(): void
    {
        // Arrange
        $user = User::factory()->create();
        $data = [
            'title' => 'Complete Note',
            'date' => '2025-01-15',
            'content' => 'Complete note content.',
        ];

        // Act
        $result = $this->action->execute($user->id, $data);

        // Assert
        $this->assertNotNull($result->id);
        $this->assertNotNull($result->created_at);
        $this->assertNotNull($result->updated_at);
        $this->assertNull($result->deleted_at);
    }
}
