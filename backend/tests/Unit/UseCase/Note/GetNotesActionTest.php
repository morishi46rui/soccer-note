<?php

declare(strict_types=1);

namespace Tests\Unit\UseCase\Note;

use App\Models\Note;
use App\Models\User;
use App\UseCase\Note\GetNotesAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class GetNotesActionTest extends TestCase
{
    use RefreshDatabase;

    private GetNotesAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new GetNotesAction();
    }

    public function test_it_returns_paginated_notes_for_user(): void
    {
        // Arrange
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        for ($i = 0; $i < 5; $i++) {
            Note::factory()->create(['user_id' => $user->id]);
        }
        for ($i = 0; $i < 3; $i++) {
            Note::factory()->create(['user_id' => $otherUser->id]);
        }

        // Act
        $result = $this->action->execute($user->id, 1, 10);

        // Assert
        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertCount(5, $result->items());
        $this->assertEquals(5, $result->total());
    }

    public function test_it_returns_notes_sorted_by_date_descending(): void
    {
        // Arrange
        $user = User::factory()->create();
        Note::factory()->create(['user_id' => $user->id, 'date' => '2025-01-01']);
        Note::factory()->create(['user_id' => $user->id, 'date' => '2025-01-03']);
        Note::factory()->create(['user_id' => $user->id, 'date' => '2025-01-02']);

        // Act
        $result = $this->action->execute($user->id, 1, 10);
        $dates = collect($result->items())->pluck('date')->map(fn($date) => $date->format('Y-m-d'))->toArray();

        // Assert
        $this->assertEquals(['2025-01-03', '2025-01-02', '2025-01-01'], $dates);
    }

    public function test_it_respects_pagination_parameters(): void
    {
        // Arrange
        $user = User::factory()->create();
        for ($i = 0; $i < 15; $i++) {
            Note::factory()->create(['user_id' => $user->id]);
        }

        // Act
        $result = $this->action->execute($user->id, 2, 5);

        // Assert
        $this->assertCount(5, $result->items());
        $this->assertEquals(2, $result->currentPage());
        $this->assertEquals(5, $result->perPage());
        $this->assertEquals(15, $result->total());
    }

    public function test_it_excludes_soft_deleted_notes(): void
    {
        // Arrange
        $user = User::factory()->create();
        for ($i = 0; $i < 3; $i++) {
            Note::factory()->create(['user_id' => $user->id]);
        }
        $deletedNote = Note::factory()->create(['user_id' => $user->id]);
        $deletedNote->delete();

        // Act
        $result = $this->action->execute($user->id, 1, 10);

        // Assert
        $this->assertCount(3, $result->items());
        $this->assertEquals(3, $result->total());
    }

    public function test_it_returns_empty_collection_when_user_has_no_notes(): void
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $result = $this->action->execute($user->id, 1, 10);

        // Assert
        $this->assertCount(0, $result->items());
        $this->assertEquals(0, $result->total());
    }
}
