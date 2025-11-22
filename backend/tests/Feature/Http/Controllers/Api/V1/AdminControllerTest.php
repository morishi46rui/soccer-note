<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Api\V1;

use App\Models\Group;
use App\Models\Note;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['email_verified_at' => null]);
    }

    public function test_it_returns_admin_stats(): void
    {
        // Arrange
        $verifiedUsers = User::factory()->count(5)->create(['email_verified_at' => now()]);
        User::factory()->count(3)->create(['email_verified_at' => null]);
        $teams = Team::factory()->count(4)->create();
        Group::factory()->count(6)->for($teams->first())->create();
        Note::factory()->count(10)->for($verifiedUsers->first())->create();

        // Act
        $response = $this->actingAs($this->user)->getJson('/api/v1/admin/stats');

        // Assert
        $response->assertOk()
            ->assertJsonStructure([
                'total_users',
                'active_users',
                'total_teams',
                'total_groups',
                'total_posts',
            ])
            ->assertJson([
                'total_users' => 9, // 5 + 3 + 1 (setUp user)
                'active_users' => 5,
                'total_teams' => 4,
                'total_groups' => 6,
                'total_posts' => 10,
            ]);
    }

    public function test_it_requires_authentication(): void
    {
        // Act
        $response = $this->getJson('/api/v1/admin/stats');

        // Assert
        $response->assertUnauthorized();
    }

    public function test_it_returns_zero_stats_when_no_data(): void
    {
        // Act
        $response = $this->actingAs($this->user)->getJson('/api/v1/admin/stats');

        // Assert
        $response->assertOk()
            ->assertJson([
                'total_users' => 1, // Only the setUp user
                'active_users' => 0, // setUp user has email_verified_at = null
                'total_teams' => 0,
                'total_groups' => 0,
                'total_posts' => 0,
            ]);
    }

    public function test_it_excludes_soft_deleted_records_from_stats(): void
    {
        // Arrange
        $verifiedUsers = User::factory()->count(2)->create(['email_verified_at' => now()]);
        User::factory()->count(1)->create(['deleted_at' => now()]);
        $teams = Team::factory()->count(3)->create();
        Team::factory()->count(1)->create(['deleted_at' => now()]);
        Group::factory()->count(2)->for($teams->first())->create();
        Group::factory()->count(1)->for($teams->first())->create(['deleted_at' => now()]);
        Note::factory()->count(4)->for($verifiedUsers->first())->create();
        Note::factory()->count(2)->for($verifiedUsers->first())->create(['deleted_at' => now()]);

        // Act
        $response = $this->actingAs($this->user)->getJson('/api/v1/admin/stats');

        // Assert
        $response->assertOk()
            ->assertJson([
                'total_users' => 3, // 2 + 1 (setUp user)
                'active_users' => 2,
                'total_teams' => 3,
                'total_groups' => 2,
                'total_posts' => 4,
            ]);
    }
}
