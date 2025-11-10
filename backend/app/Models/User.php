<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'User',
    properties: [
        new OA\Property(property: 'id', type: 'integer', format: 'int64', description: 'ユーザーID'),
        new OA\Property(property: 'name', type: 'string', description: 'ユーザー名'),
        new OA\Property(property: 'email', type: 'string', format: 'email', description: 'メールアドレス'),
        new OA\Property(property: 'email_verified_at', type: 'string', format: 'date-time', nullable: true, description: 'メールアドレス確認日時'),
        new OA\Property(property: 'password', type: 'string', format: 'password', description: 'パスワード'),
        new OA\Property(property: 'remember_token', type: 'string', nullable: true, description: 'リメンバートークン'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', description: '作成日時'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', description: '更新日時'),
    ]
)]

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
