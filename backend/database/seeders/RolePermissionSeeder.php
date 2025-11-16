<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 権限の作成
        $permissions = [
            [
                'name' => 'view_notes',
                'display_name' => 'ノート閲覧',
                'description' => 'ノートを閲覧できる',
            ],
            [
                'name' => 'edit_notes',
                'display_name' => 'ノート編集',
                'description' => 'ノートを編集できる',
            ],
            [
                'name' => 'delete_notes',
                'display_name' => 'ノート削除',
                'description' => 'ノートを削除できる',
            ],
            [
                'name' => 'manage_team',
                'display_name' => 'チーム管理',
                'description' => 'チームを管理できる',
            ],
            [
                'name' => 'manage_group',
                'display_name' => 'グループ管理',
                'description' => 'グループを管理できる',
            ],
            [
                'name' => 'manage_members',
                'display_name' => 'メンバー管理',
                'description' => 'メンバーを管理できる',
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                $permission
            );
        }

        // ロールの作成
        $player = Role::firstOrCreate(
            ['name' => 'player'],
            [
                'display_name' => '選手',
                'description' => 'チームの選手',
            ]
        );

        $coach = Role::firstOrCreate(
            ['name' => 'coach'],
            [
                'display_name' => 'コーチ',
                'description' => 'チームのコーチ',
            ]
        );

        $manager = Role::firstOrCreate(
            ['name' => 'manager'],
            [
                'display_name' => 'マネージャー',
                'description' => 'チームのマネージャー',
            ]
        );

        $admin = Role::firstOrCreate(
            ['name' => 'admin'],
            [
                'display_name' => '管理者',
                'description' => 'チームの管理者',
            ]
        );

        // ロールと権限の紐付け
        // 選手: ノート閲覧のみ
        $player->permissions()->sync(
            Permission::whereIn('name', ['view_notes'])->pluck('id')
        );

        // コーチ: ノート閲覧、編集、削除
        $coach->permissions()->sync(
            Permission::whereIn('name', ['view_notes', 'edit_notes', 'delete_notes'])->pluck('id')
        );

        // マネージャー: ノート閲覧、編集、グループ管理
        $manager->permissions()->sync(
            Permission::whereIn('name', ['view_notes', 'edit_notes', 'manage_group'])->pluck('id')
        );

        // 管理者: すべての権限
        $admin->permissions()->sync(
            Permission::all()->pluck('id')
        );
    }
}
