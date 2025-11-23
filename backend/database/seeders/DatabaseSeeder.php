<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 権限とロールのシード
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
        ]);

        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'miyamori',
            'email' => 'miyamori@example.com',
            'password' => bcrypt('password'),
        ]);
    }
}
