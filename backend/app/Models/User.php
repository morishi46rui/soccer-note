<?php

namespace App\Models;

use App\Traits\HasSqid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'User',
    properties: [
        new OA\Property(property: 'id', type: 'integer', format: 'int64', description: 'ユーザーID'),
        new OA\Property(property: 'sqid', type: 'string', description: 'Sqid (公開用ID)', example: 'xYz34WvU'),
        new OA\Property(property: 'name', type: 'string', description: 'ユーザー名'),
        new OA\Property(property: 'email', type: 'string', format: 'email', description: 'メールアドレス'),
        new OA\Property(property: 'email_verified_at', type: 'string', format: 'date-time', nullable: true, description: 'メールアドレス確認日時'),
        new OA\Property(property: 'password', type: 'string', format: 'password', description: 'パスワード'),
        new OA\Property(property: 'remember_token', type: 'string', nullable: true, description: 'リメンバートークン'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', description: '作成日時'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', description: '更新日時'),
        new OA\Property(property: 'deleted_at', type: 'string', format: 'date-time', nullable: true, description: '論理削除日時'),
    ]
)]

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasSqid, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = [
        'sqid',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * ユーザーが持つロール
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }
}
