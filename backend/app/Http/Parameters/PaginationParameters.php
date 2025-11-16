<?php

declare(strict_types=1);

namespace App\Http\Parameters;

use OpenApi\Attributes as OA;

#[OA\Parameter(
    parameter: 'page',
    name: 'page',
    in: 'query',
    description: 'ページ番号',
    schema: new OA\Schema(type: 'integer', default: 1)
)]
#[OA\Parameter(
    parameter: 'per_page',
    name: 'per_page',
    in: 'query',
    description: '1ページあたりの件数',
    schema: new OA\Schema(type: 'integer', default: 15)
)]
class PaginationParameters {}
