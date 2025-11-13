<?php

namespace App\Models;

use App\Traits\HasSqid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Note',
    properties: [
        new OA\Property(property: 'id', type: 'integer', format: 'int64', description: 'ノートID'),
        new OA\Property(property: 'sqid', type: 'string', description: 'Sqid (公開用ID)', example: 'aBc12DeF'),
        new OA\Property(property: 'user_id', type: 'integer', format: 'int64', description: 'ユーザーID'),
        new OA\Property(property: 'title', type: 'string', description: 'タイトル'),
        new OA\Property(property: 'date', type: 'string', format: 'date', description: '日付'),
        new OA\Property(property: 'content', type: 'string', description: '内容'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', description: '作成日時'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', description: '更新日時'),
        new OA\Property(property: 'deleted_at', type: 'string', format: 'date-time', nullable: true, description: '論理削除日時'),
    ]
)]
class Note extends Model
{
    use HasFactory, HasSqid, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'date',
        'content',
    ];

    protected $casts = [
        'date' => 'date:Y-m-d',
    ];

    protected $appends = [
        'sqid',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
