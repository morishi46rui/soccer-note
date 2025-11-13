<?php

declare(strict_types=1);

namespace Tests\Unit\Traits;

use App\Models\Note;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HasSqidTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_generates_sqid_attribute(): void
    {
        // Arrange
        $user = User::factory()->create();
        $note = Note::factory()->create(['user_id' => $user->id]);

        // Act
        $sqid = $note->sqid;

        // Assert
        $this->assertIsString($sqid);
        $this->assertGreaterThanOrEqual(8, strlen($sqid));
    }

    public function test_it_finds_note_by_sqid(): void
    {
        // Arrange
        $user = User::factory()->create();
        $note = Note::factory()->create(['user_id' => $user->id]);
        $sqid = $note->sqid;

        // Act
        $foundNote = Note::findBySqid($sqid);

        // Assert
        $this->assertNotNull($foundNote);
        $this->assertEquals($note->id, $foundNote->id);
    }

    public function test_it_returns_null_for_invalid_sqid(): void
    {
        // Arrange
        $invalidSqid = 'invalid123';

        // Act
        $result = Note::findBySqid($invalidSqid);

        // Assert
        $this->assertNull($result);
    }

    public function test_it_throws_exception_for_invalid_sqid_with_or_fail(): void
    {
        // Arrange
        $invalidSqid = 'invalid123';

        // Assert
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        // Act
        Note::findBySqidOrFail($invalidSqid);
    }

    public function test_it_includes_sqid_in_json(): void
    {
        // Arrange
        $user = User::factory()->create();
        $note = Note::factory()->create(['user_id' => $user->id]);

        // Act
        $json = $note->toArray();

        // Assert
        $this->assertArrayHasKey('sqid', $json);
        $this->assertIsString($json['sqid']);
    }

    public function test_sqid_is_consistent_for_same_id(): void
    {
        // Arrange
        $user = User::factory()->create();
        $note = Note::factory()->create(['user_id' => $user->id]);

        // Act
        $sqid1 = $note->sqid;
        $sqid2 = $note->sqid;

        // Assert
        $this->assertEquals($sqid1, $sqid2);
    }

    public function test_different_notes_have_different_sqids(): void
    {
        // Arrange
        $user = User::factory()->create();
        $note1 = Note::factory()->create(['user_id' => $user->id]);
        $note2 = Note::factory()->create(['user_id' => $user->id]);

        // Act
        $sqid1 = $note1->sqid;
        $sqid2 = $note2->sqid;

        // Assert
        $this->assertNotEquals($sqid1, $sqid2);
    }
}
