<?php

/**
 * V1コントローラーからルーティングを自動生成するスクリプト
 */

$controllersPath = __DIR__ . '/../backend/app/Http/Controllers/Api/V1';
$routesPath = __DIR__ . '/../backend/routes/api.php';

// V1ディレクトリ内の全てのコントローラーを取得
$controllers = glob($controllersPath . '/*Controller.php');

$routes = [];

foreach ($controllers as $controllerFile) {
    $className = basename($controllerFile, '.php');
    $content = file_get_contents($controllerFile);

    $useStatement = "use App\\Http\\Controllers\\Api\\V1\\{$className}";

    // #[OA\(メソッド名)で始まるブロックを全て見つける
    if (!preg_match_all('/#\[OA\\\\(Get|Post|Put|Delete|Patch)\(.*?path:\s*[\'"]\/api\/v1\/([^\'\"]+)[\'"].*?\)]\s*(?:#\[[^\]]+\]\s*)*public\s+function\s+(\w+)/s', $content, $matches, PREG_SET_ORDER)) {
        continue;
    }

    foreach ($matches as $match) {
        $httpMethod = strtolower($match[1]);
        $path = $match[2];
        $methodName = $match[3];

        // このマッチ全体からsecurityを探す
        $requiresAuth = (bool)preg_match('/security:\s*\[\[/', $match[0]);

        $routes[] = [
            'use' => $useStatement,
            'method' => $httpMethod,
            'path' => $path,
            'controller' => $className,
            'action' => $methodName,
            'requiresAuth' => $requiresAuth
        ];
    }
}

// ルーティングファイルを生成
$output = "<?php\n\n";

// use文を追加
$uses = array_unique(array_column($routes, 'use'));
foreach ($uses as $use) {
    $output .= "$use;\n";
}

$output .= "use Illuminate\\Support\\Facades\\Route;\n\n";
$output .= "Route::prefix('v1')->group(function () {\n";

// 認証不要なルートと認証必要なルートを分ける
$publicRoutes = array_filter($routes, fn($r) => !$r['requiresAuth']);
$protectedRoutes = array_filter($routes, fn($r) => $r['requiresAuth']);

// 認証不要なルート
foreach ($publicRoutes as $route) {
    $output .= "    Route::{$route['method']}('/{$route['path']}', [{$route['controller']}::class, '{$route['action']}']);\n";
}

// 認証必要なルート
if (!empty($protectedRoutes)) {
    $output .= "\n    // Sanctum認証が必要なエンドポイント\n";
    $output .= "    Route::middleware('auth:sanctum')->group(function () {\n";
    foreach ($protectedRoutes as $route) {
        $output .= "        Route::{$route['method']}('/{$route['path']}', [{$route['controller']}::class, '{$route['action']}']);\n";
    }
    $output .= "    });\n";
}

$output .= "});\n";

file_put_contents($routesPath, $output);

echo "✓ ルーティングファイルを生成しました\n";
echo "✓ " . count($routes) . "個のルートを追加しました\n";
echo "  - 認証不要: " . count($publicRoutes) . "個\n";
echo "  - 認証必要: " . count($protectedRoutes) . "個\n";
