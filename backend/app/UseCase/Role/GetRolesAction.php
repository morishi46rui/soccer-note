<?php

declare(strict_types=1);

namespace App\UseCase\Role;

use App\Models\Role;
use Illuminate\Database\Eloquent\Collection;

class GetRolesAction
{
    /**
     * すべてのロールを取得（権限情報も含む）
     */
    public function execute(): Collection
    {
        return Role::with('permissions')->get();
    }
}
