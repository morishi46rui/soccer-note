<?php

declare(strict_types=1);

namespace App\UseCase\Permission;

use App\Models\Permission;
use Illuminate\Database\Eloquent\Collection;

class GetPermissionsAction
{
    /**
     * すべての権限を取得
     */
    public function execute(): Collection
    {
        return Permission::all();
    }
}
