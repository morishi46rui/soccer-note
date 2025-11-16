<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // ノート関連の権限
            [
                'name' => 'view_notes',
                'display_name' => 'ノート閲覧',
                'description' => 'ノートを閲覧できる',
            ],
            [
                'name' => 'create_notes',
                'display_name' => 'ノート作成',
                'description' => 'ノートを作成できる',
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

            // チーム関連の権限
            [
                'name' => 'view_team',
                'display_name' => 'チーム閲覧',
                'description' => 'チーム情報を閲覧できる',
            ],
            [
                'name' => 'create_team',
                'display_name' => 'チーム作成',
                'description' => 'チームを作成できる',
            ],
            [
                'name' => 'edit_team',
                'display_name' => 'チーム編集',
                'description' => 'チーム情報を編集できる',
            ],
            [
                'name' => 'delete_team',
                'display_name' => 'チーム削除',
                'description' => 'チームを削除できる',
            ],

            // グループ関連の権限
            [
                'name' => 'view_group',
                'display_name' => 'グループ閲覧',
                'description' => 'グループ情報を閲覧できる',
            ],
            [
                'name' => 'create_group',
                'display_name' => 'グループ作成',
                'description' => 'グループを作成できる',
            ],
            [
                'name' => 'edit_group',
                'display_name' => 'グループ編集',
                'description' => 'グループ情報を編集できる',
            ],
            [
                'name' => 'delete_group',
                'display_name' => 'グループ削除',
                'description' => 'グループを削除できる',
            ],

            // メンバー関連の権限
            [
                'name' => 'view_members',
                'display_name' => 'メンバー閲覧',
                'description' => 'メンバー情報を閲覧できる',
            ],
            [
                'name' => 'add_members',
                'display_name' => 'メンバー追加',
                'description' => 'メンバーを追加できる',
            ],
            [
                'name' => 'edit_members',
                'display_name' => 'メンバー編集',
                'description' => 'メンバー情報を編集できる',
            ],
            [
                'name' => 'remove_members',
                'display_name' => 'メンバー削除',
                'description' => 'メンバーを削除できる',
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                $permission
            );
        }
    }
}
