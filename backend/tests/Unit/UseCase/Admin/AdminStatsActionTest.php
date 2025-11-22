<?php

declare(strict_types=1);

namespace Tests\Unit\UseCase\Admin;

use App\Models\Group;
use App\Models\Note;
use App\Models\Team;
use App\Models\User;
use App\UseCase\Admin\AdminStatsAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminStatsActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_correct_stats(): void
    {
        // Arrange
        $verifiedUsers = User::factory()->count(5)->create(['email_verified_at' => now()]);
        User::factory()->count(3)->create(['email_verified_at' => null]);
        $teams = Team::factory()->count(4)->create();
        Group::factory()->count(6)->for($teams->first())->create();
        Note::factory()->count(10)->for($verifiedUsers->first())->create();

        $action = new AdminStatsAction;

        // Act
        $result = $action->execute();

        // Assert
        $this->assertEquals(8, $result['total_users']);
        $this->assertEquals(5, $result['active_users']);
        $this->assertEquals(4, $result['total_teams']);
        $this->assertEquals(6, $result['total_groups']);
        $this->assertEquals(10, $result['total_posts']);
    }

    public function test_it_returns_zero_when_no_data(): void
    {
        // Arrange
        $action = new AdminStatsAction;

        // Act
        $result = $action->execute();

        // Assert
        $this->assertEquals(0, $result['total_users']);
        $this->assertEquals(0, $result['active_users']);
        $this->assertEquals(0, $result['total_teams']);
        $this->assertEquals(0, $result['total_groups']);
        $this->assertEquals(0, $result['total_posts']);
    }

    public function test_it_excludes_soft_deleted_records(): void
    {
        // Arrange
        $verifiedUsers = User::factory()->count(3)->create(['email_verified_at' => now()]);
        User::factory()->count(2)->create(['deleted_at' => now()]);
        $teams = Team::factory()->count(2)->create();
        Team::factory()->count(1)->create(['deleted_at' => now()]);
        Group::factory()->count(4)->for($teams->first())->create();
        Group::factory()->count(2)->for($teams->first())->create(['deleted_at' => now()]);
        Note::factory()->count(5)->for($verifiedUsers->first())->create();
        Note::factory()->count(3)->for($verifiedUsers->first())->create(['deleted_at' => now()]);

        $action = new AdminStatsAction;

        // Act
        $result = $action->execute();

        // Assert
        $this->assertEquals(3, $result['total_users']);
        $this->assertEquals(3, $result['active_users']);
        $this->assertEquals(2, $result['total_teams']);
        $this->assertEquals(4, $result['total_groups']);
        $this->assertEquals(5, $result['total_posts']);
    }

    public function test_it_returns_all_expected_keys(): void
    {
        // Arrange
        $action = new AdminStatsAction;

        // Act
        $result = $action->execute();

        // Assert
        $this->assertArrayHasKey('total_users', $result);
        $this->assertArrayHasKey('active_users', $result);
        $this->assertArrayHasKey('total_teams', $result);
        $this->assertArrayHasKey('total_groups', $result);
        $this->assertArrayHasKey('total_posts', $result);
    }
}
