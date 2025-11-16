<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasSqid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Team',
    properties: [
        new OA\Property(property: 'id', type: 'integer', format: 'int64', description: 'チームID'),
        new OA\Property(property: 'sqid', type: 'string', description: 'Sqid (公開用ID)', example: 'aBc12DeF'),
        new OA\Property(property: 'name', type: 'string', description: 'チーム名'),
        new OA\Property(property: 'description', type: 'string', nullable: true, description: 'チームの説明'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', description: '作成日時'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', description: '更新日時'),
    ]
)]
class Team extends Model
{
    use HasFactory, HasSqid;

    protected $fillable = [
        'name',
        'description',
    ];

    protected $appends = [
        'sqid',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'team_user')
            ->withPivot('role_id')
            ->withTimestamps();
    }
}
