<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Permission',
    properties: [
        new OA\Property(property: 'id', type: 'integer', format: 'int64', description: '権限ID'),
        new OA\Property(property: 'name', type: 'string', description: '権限識別子'),
        new OA\Property(property: 'display_name', type: 'string', description: '表示名'),
        new OA\Property(property: 'description', type: 'string', nullable: true, description: '権限の説明'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', description: '作成日時'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', description: '更新日時'),
        new OA\Property(property: 'deleted_at', type: 'string', format: 'date-time', nullable: true, description: '論理削除日時'),
    ]
)]
class Permission extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'display_name',
        'description',
    ];
}
