<?php

declare(strict_types=1);

namespace Database\Seeders;

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
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => '管理者',
                'password' => bcrypt('password'),
            ]
        );

        // テスト用プレイヤー
        User::firstOrCreate(
            ['email' => 'player@example.com'],
            [
                'name' => '選手',
                'password' => bcrypt('password'),
            ]
        );
    }
}
