<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'Soccer Note API',
    description: 'サッカーノートアプリケーションのAPI仕様',
    contact: new OA\Contact(email: 'admin@example.com')
)]
#[OA\Server(
    url: 'http://localhost:8000',
    description: '開発環境'
)]
abstract class Controller
{
    //
}
