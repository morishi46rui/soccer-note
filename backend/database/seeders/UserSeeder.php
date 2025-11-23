<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 管理者ユーザーの作成
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => '管理者',
                'password' => bcrypt('password'),
            ]
        );

        // admin ロールを割り当て
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminUser->roles()->syncWithoutDetaching([$adminRole->id]);
        }

        // テスト用プレイヤー
        $playerUser = User::firstOrCreate(
            ['email' => 'player@example.com'],
            [
                'name' => '選手',
                'password' => bcrypt('password'),
            ]
        );

        // player ロールを割り当て
        $playerRole = Role::where('name', 'player')->first();
        if ($playerRole) {
            $playerUser->roles()->syncWithoutDetaching([$playerRole->id]);
        }
    }
}
