<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ロールの作成
        $admin = Role::firstOrCreate(
            ['name' => 'admin'],
            [
                'display_name' => '管理者',
                'description' => 'チームの管理者',
            ]
        );

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

        // 管理者: すべての権限
        $admin->permissions()->sync(
            Permission::all()->pluck('id')
        );

        // ロールと権限の紐付け
        // 選手: ノート閲覧のみ、チーム・グループ・メンバー閲覧
        $player->permissions()->sync(
            Permission::whereIn('name', [
                'view_notes',
                'view_team',
                'view_group',
                'view_members',
            ])->pluck('id')
        );

        // コーチ: ノート全操作、グループ管理、メンバー閲覧・編集
        $coach->permissions()->sync(
            Permission::whereIn('name', [
                'view_notes',
                'create_notes',
                'edit_notes',
                'delete_notes',
                'view_team',
                'view_group',
                'create_group',
                'edit_group',
                'delete_group',
                'view_members',
                'edit_members',
            ])->pluck('id')
        );

    }
}
