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

    // OA\Get, OA\Post などのHTTPメソッドを抽出
    preg_match_all('/#\[OA\\\\(Get|Post|Put|Delete|Patch)\(/', $content, $matches);

    if (!empty($matches[1])) {
        $useStatement = "use App\\Http\\Controllers\\Api\\V1\\{$className};";

        foreach ($matches[1] as $method) {
            // パスを抽出
            if (preg_match('/path:\s*[\'"]\/api\/v1\/([^\'"]+)[\'"]/', $content, $pathMatch)) {
                $path = $pathMatch[1];
                $httpMethod = strtolower($method);
                $methodName = 'index'; // デフォルト

                // メソッド名を推測
                if ($httpMethod === 'post') $methodName = 'store';
                elseif ($httpMethod === 'put' || $httpMethod === 'patch') $methodName = 'update';
                elseif ($httpMethod === 'delete') $methodName = 'destroy';

                $routes[] = [
                    'use' => $useStatement,
                    'method' => $httpMethod,
                    'path' => $path,
                    'controller' => $className,
                    'action' => $methodName
                ];
            }
        }
    }
}

// ルーティングファイルを生成
$output = "<?php\n\n";

// use文を追加
$uses = array_unique(array_column($routes, 'use'));
foreach ($uses as $use) {
    $output .= "$use\n";
}

$output .= "use Illuminate\\Support\\Facades\\Route;\n\n";
$output .= "Route::prefix('v1')->group(function () {\n";

foreach ($routes as $route) {
    $output .= "    Route::{$route['method']}('/{$route['path']}', [{$route['controller']}::class, '{$route['action']}']);\n";
}

$output .= "});\n";

file_put_contents($routesPath, $output);

echo "✓ ルーティングファイルを生成しました\n";
echo "✓ " . count($routes) . "個のルートを追加しました\n";
