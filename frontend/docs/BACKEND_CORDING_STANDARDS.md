# Backend Coding Standards

このドキュメントは、Soccer Note バックエンド開発における PHP/Laravel のコーディング規約を定義します。

**重要**: バックエンドのコードを書く前に、必ずこのドキュメントを読んで規約を確認してください。

## Table of Contents

- [General Principles](#general-principles)
- [Architecture](#architecture)
- [Controllers](#controllers)
- [Use Cases (Actions)](#use-cases-actions)
- [Models](#models)
- [Request Validation](#request-validation)
- [OpenAPI Documentation](#openapi-documentation)
- [Database](#database)
- [Error Handling](#error-handling)
- [Testing](#testing)
- [Best Practices](#best-practices)

## General Principles

### Strict Types

すべての PHP ファイルの先頭に `declare(strict_types=1);` を記述してください。

```php
<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

// ...
```

### Namespace

Laravel の PSR-4 オートローディング規約に従ってください。

- Controllers: `App\Http\Controllers\Api\V1\`
- Models: `App\Models\`
- Use Cases: `App\UseCase\{Feature}\`
- Requests: `App\Http\Requests\{Feature}\`
- Traits: `App\Traits\`

### Type Hints

すべてのメソッドの引数と戻り値に型ヒントを記述してください。

```php
// ✅ Good: 型ヒントが明示されている
public function execute(int $userId, array $data): Note
{
    return Note::create([
        'user_id' => $userId,
        'title' => $data['title'],
        'date' => $data['date'],
        'content' => $data['content'],
    ]);
}

// ❌ Bad: 型ヒントがない
public function execute($userId, $data)
{
    return Note::create([
        'user_id' => $userId,
        'title' => $data['title'],
        'date' => $data['date'],
        'content' => $data['content'],
    ]);
}
```

## Architecture

### Layered Architecture

このプロジェクトは以下のレイヤー構造を採用しています：

```
Controller (HTTP Layer)
    ↓
Request Validation (Input Validation)
    ↓
Use Case / Action (Business Logic)
    ↓
Model (Data Access)
```

### Dependency Injection

Laravel の Service Container による依存性注入を使用してください。コンストラクタインジェクションではなく、メソッドインジェクションを採用しています。

```php
// ✅ Good: メソッドインジェクション
public function store(CreateNoteRequest $request, CreateNoteAction $action): JsonResponse
{
    $note = $action->execute($request->user()->id, $request->validated());
    return response()->json($note, 201, [], JSON_UNESCAPED_UNICODE);
}
```

## Controllers

### API Versioning

すべての API コントローラーは `App\Http\Controllers\Api\V1\` 名前空間に配置してください。

### Controller Responsibilities

コントローラーは以下の責務のみを持ちます：

1. リクエストパラメータの取得
2. Use Case (Action) の呼び出し
3. レスポンスの返却

ビジネスロジックをコントローラーに記述してはいけません。

```php
// ✅ Good: ビジネスロジックは Action に委譲
public function store(CreateNoteRequest $request, CreateNoteAction $action): JsonResponse
{
    $note = $action->execute($request->user()->id, $request->validated());
    return response()->json($note, 201, [], JSON_UNESCAPED_UNICODE);
}

// ❌ Bad: コントローラー内にビジネスロジックを記述
public function store(CreateNoteRequest $request): JsonResponse
{
    $note = Note::create([
        'user_id' => $request->user()->id,
        'title' => $request->title,
        'date' => $request->date,
        'content' => $request->content,
    ]);

    // 関連処理...
    // さらに複雑なロジック...

    return response()->json($note, 201);
}
```

### JSON Response Format

JSON レスポンスには必ず `JSON_UNESCAPED_UNICODE` フラグを指定してください（日本語の文字化け防止）。

```php
// ✅ Good
return response()->json($data, 200, [], JSON_UNESCAPED_UNICODE);

// ❌ Bad
return response()->json($data, 200);
```

### HTTP Status Codes

適切な HTTP ステータスコードを使用してください：

- `200 OK`: 正常な取得・更新
- `201 Created`: リソースの作成成功
- `204 No Content`: 削除成功（レスポンスボディなし）
- `401 Unauthorized`: 認証エラー
- `404 Not Found`: リソースが見つからない
- `422 Unprocessable Entity`: バリデーションエラー

### Sqid and Numeric ID Support

リソースの識別子として、Sqid（短縮 ID）と数値 ID の両方をサポートしてください。

```php
// ✅ Good: Sqid と数値ID の両方をサポート
public function show(string $id, Request $request, GetNoteAction $action): JsonResponse
{
    // SqidまたはIDを受け入れる
    $noteId = is_numeric($id) ? (int) $id : Note::findBySqid($id)?->id;

    if ($noteId === null) {
        return response()->json([
            'message' => 'ノートが見つかりませんでした',
        ], 404, [], JSON_UNESCAPED_UNICODE);
    }

    $note = $action->execute($noteId, $request->user()->id);

    if ($note === null) {
        return response()->json([
            'message' => 'ノートが見つかりません',
        ], 404, [], JSON_UNESCAPED_UNICODE);
    }

    return response()->json($note, 200, [], JSON_UNESCAPED_UNICODE);
}
```

## Use Cases (Actions)

### Action Pattern

ビジネスロジックは Use Case として Action クラスに実装してください。

- 配置場所: `app/UseCase/{Feature}/{ActionName}.php`
- 命名規則: `{Verb}{Feature}Action` (例: `CreateNoteAction`, `GetNotesAction`)

### Single Responsibility

各 Action クラスは単一の責務のみを持ち、`execute` メソッドで処理を実行してください。

```php
// ✅ Good: 単一責務の Action
<?php

declare(strict_types=1);

namespace App\UseCase\Note;

use App\Models\Note;

class CreateNoteAction
{
    public function execute(int $userId, array $data): Note
    {
        return Note::create([
            'user_id' => $userId,
            'title' => $data['title'],
            'date' => $data['date'],
            'content' => $data['content'],
        ]);
    }
}
```

### Return Types

Action は以下のいずれかを返してください：

- Model インスタンス
- Collection
- 配列
- `null`（見つからない場合）

エラーハンドリングは Controller 層で行います。

```php
// ✅ Good: null を返す
public function execute(int $noteId, int $userId): ?Note
{
    return Note::where('id', $noteId)
        ->where('user_id', $userId)
        ->first();
}

// ❌ Bad: Action 内で例外を投げる
public function execute(int $noteId, int $userId): Note
{
    $note = Note::find($noteId);

    if (!$note) {
        throw new \Exception('ノートが見つかりません');
    }

    return $note;
}
```

## Models

### Model Properties

モデルには以下のプロパティを適切に定義してください：

- `$fillable`: マスアサインメント可能な属性
- `$hidden`: JSON シリアライズ時に隠す属性
- `$casts`: 属性の型キャスト
- `$appends`: JSON に自動追加する属性

```php
// ✅ Good: プロパティが適切に定義されている
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
}
```

### Soft Deletes

重要なリソースには論理削除（Soft Deletes）を使用してください。

```php
use Illuminate\Database\Eloquent\SoftDeletes;

class Note extends Model
{
    use SoftDeletes;
}
```

### Relationships

リレーションシップは型ヒント付きで定義してください。

```php
use Illuminate\Database\Eloquent\Relations\BelongsTo;

public function user(): BelongsTo
{
    return $this->belongsTo(User::class);
}
```

### Traits

共通機能は Trait として切り出してください。

このプロジェクトでは `HasSqid` Trait を使用して、すべてのモデルに短縮 ID（Sqid）機能を提供しています。

```php
use App\Traits\HasSqid;

class Note extends Model
{
    use HasSqid;
}

// Sqid を使用
$note = Note::findBySqid('aBc12DeF');
$sqid = $note->sqid; // アクセサーで取得
```

## Request Validation

### Form Request Classes

バリデーションは必ず Form Request クラスを使用してください。

- 配置場所: `app/Http/Requests/{Feature}/{ActionName}Request.php`
- 命名規則: `{ActionName}Request` (例: `CreateNoteRequest`, `UpdateNoteRequest`)

```php
// ✅ Good: Form Request クラスを使用
<?php

namespace App\Http\Requests\Note;

use Illuminate\Foundation\Http\FormRequest;

class CreateNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'content' => ['required', 'string'],
        ];
    }
}
```

### Validation Rules

バリデーションルールは配列形式で記述してください。

```php
// ✅ Good: 配列形式
return [
    'title' => ['required', 'string', 'max:255'],
    'email' => ['required', 'email', 'unique:users'],
];

// ❌ Bad: パイプ形式
return [
    'title' => 'required|string|max:255',
    'email' => 'required|email|unique:users',
];
```

### Authorization

`authorize()` メソッドで認証チェックを行います。通常は `true` を返しますが、リソース所有者チェックなどが必要な場合は適切な判定を行ってください。

## OpenAPI Documentation

### Swagger Attributes

すべての API エンドポイントには OpenAPI アトリビュートを記述してください。

### Controller Tags

各コントローラーに `#[OA\Tag]` を記述してください。

```php
#[OA\Tag(name: 'Notes', description: 'ノート管理')]
class NoteController extends Controller
{
    // ...
}
```

### Endpoint Documentation

各エンドポイントに以下を記述してください：

- `#[OA\{Method}]`: HTTP メソッドとパス
- `#[OA\RequestBody]`: リクエストボディ（POST/PUT の場合）
- `#[OA\Response]`: 各ステータスコードのレスポンス
- `#[OA\Parameter]`: パスパラメータやクエリパラメータ

```php
#[OA\Post(
    path: '/api/v1/notes',
    tags: ['Notes'],
    summary: 'ノート作成',
    description: '新しいノートを作成します',
    operationId: 'createNote',
    security: [['sanctum' => []]]
)]
#[OA\RequestBody(
    required: true,
    content: new OA\JsonContent(ref: '#/components/schemas/CreateNoteRequest')
)]
#[OA\Response(
    response: '201',
    description: '作成成功',
    content: new OA\JsonContent(ref: '#/components/schemas/CreateNoteResponse')
)]
#[OA\Response(response: '401', ref: '#/components/responses/401')]
#[OA\Response(response: '422', ref: '#/components/responses/422')]
public function store(CreateNoteRequest $request, CreateNoteAction $action): JsonResponse
{
    // ...
}
```

### Schema Definitions

モデル、リクエスト、レスポンスには `#[OA\Schema]` を記述してください。

```php
#[OA\Schema(
    schema: 'Note',
    properties: [
        new OA\Property(property: 'id', type: 'integer', format: 'int64', description: 'ノートID'),
        new OA\Property(property: 'sqid', type: 'string', description: 'Sqid (公開用ID)', example: 'aBc12DeF'),
        new OA\Property(property: 'title', type: 'string', description: 'タイトル'),
        new OA\Property(property: 'date', type: 'string', format: 'date', description: '日付'),
        new OA\Property(property: 'content', type: 'string', description: '内容'),
    ]
)]
class Note extends Model
{
    // ...
}
```

### Shared Components

共通のエラーレスポンスは `app/Http/Responses/ErrorResponses.php` に定義してください。

```php
#[OA\Response(
    response: '401',
    description: '認証エラー',
    content: new OA\JsonContent(
        properties: [
            new OA\Property(property: 'message', type: 'string', example: '認証に失敗しました'),
        ]
    )
)]
class ErrorResponses
{
}
```

### Documentation Generation

API ドキュメントを生成するには：

```bash
make api
```

これにより以下が実行されます：

1. ルート自動生成（`php scripts/generate-routes.php`）
2. Swagger ドキュメント生成（`php artisan l5-swagger:generate`）
3. TypeScript 型定義生成（フロントエンド用）

## Database

### Migrations

マイグレーションファイルには以下を含めてください：

- テーブル名
- カラム定義（型、NULL 可否、デフォルト値）
- インデックス
- 外部キー制約
- タイムスタンプ

```php
public function up(): void
{
    Schema::create('notes', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->string('title');
        $table->date('date');
        $table->text('content');
        $table->timestamps();
        $table->softDeletes();

        $table->index('user_id');
        $table->index('date');
    });
}
```

### Foreign Keys

外部キーには `constrained()` を使用し、削除時の動作を明示してください。

```php
// ✅ Good
$table->foreignId('user_id')->constrained()->onDelete('cascade');

// ❌ Bad
$table->unsignedBigInteger('user_id');
```

### Indexes

検索やフィルタリングに使用されるカラムにはインデックスを追加してください。

```php
$table->index('user_id');
$table->index('date');
$table->index(['user_id', 'date']); // 複合インデックス
```

## Error Handling

### Null Checks

リソースが見つからない場合は `null` を返し、コントローラー層で 404 レスポンスを返してください。

```php
// Action
public function execute(int $noteId, int $userId): ?Note
{
    return Note::where('id', $noteId)
        ->where('user_id', $userId)
        ->first();
}

// Controller
$note = $action->execute($noteId, $request->user()->id);

if ($note === null) {
    return response()->json([
        'message' => 'ノートが見つかりません',
    ], 404, [], JSON_UNESCAPED_UNICODE);
}
```

### Error Messages

エラーメッセージは日本語で、ユーザーにわかりやすいものにしてください。

```php
// ✅ Good: わかりやすいエラーメッセージ
return response()->json([
    'message' => 'ノートが見つかりませんでした',
], 404, [], JSON_UNESCAPED_UNICODE);

// ❌ Bad: 英語や技術的すぎるメッセージ
return response()->json([
    'message' => 'Resource not found',
], 404);
```

### Authentication Errors

認証エラーは 401 ステータスで返してください。

```php
if ($result === null) {
    return response()->json([
        'message' => '認証に失敗しました',
    ], 401, [], JSON_UNESCAPED_UNICODE);
}
```

## Testing

### Feature Tests

API エンドポイントには必ず Feature Test を書いてください。

- 配置場所: `tests/Feature/{Feature}/`

```php
// tests/Feature/Note/CreateNoteTest.php
public function test_can_create_note(): void
{
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->postJson('/api/v1/notes', [
            'title' => 'Test Note',
            'date' => '2025-11-15',
            'content' => 'Test content',
        ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'id',
            'user_id',
            'title',
            'date',
            'content',
            'created_at',
            'updated_at',
        ]);

    $this->assertDatabaseHas('notes', [
        'user_id' => $user->id,
        'title' => 'Test Note',
    ]);
}
```

### Test Organization

テストは機能ごとにディレクトリを分けてください。

```
tests/
├── Feature/
│   ├── Auth/
│   │   ├── LoginTest.php
│   │   ├── RegisterTest.php
│   │   └── LogoutTest.php
│   └── Note/
│       ├── CreateNoteTest.php
│       ├── GetNotesTest.php
│       └── UpdateNoteTest.php
└── Unit/
```

### Authentication in Tests

認証が必要なエンドポイントは `actingAs()` でユーザーを認証してください。

```php
$user = User::factory()->create();

$response = $this->actingAs($user)
    ->getJson('/api/v1/notes');
```

## Best Practices

### Use Laravel Conventions

Laravel の命名規約とベストプラクティスに従ってください：

- テーブル名: 複数形のスネークケース（`notes`, `users`）
- モデル名: 単数形のパスカルケース（`Note`, `User`）
- コントローラー名: `{Model}Controller`
- 変数名: キャメルケース（`$userId`, `$noteId`）

### Avoid Over-Engineering

シンプルさを保ってください。必要以上に抽象化しないでください。

```php
// ✅ Good: シンプルで読みやすい
public function execute(int $userId, array $data): Note
{
    return Note::create([
        'user_id' => $userId,
        'title' => $data['title'],
        'date' => $data['date'],
        'content' => $data['content'],
    ]);
}

// ❌ Bad: 過度な抽象化
public function execute(CreateNoteDTO $dto): Note
{
    $factory = new NoteFactory();
    $repository = new NoteRepository();
    $validator = new NoteValidator();

    // 複雑すぎる...
}
```

### Code Formatting

Laravel Pint でコードをフォーマットしてください。

```bash
make pint
```

### Security

- パスワードは必ずハッシュ化してください（`bcrypt` または `Hash::make`）
- ユーザー入力は必ずバリデーションしてください
- SQL インジェクション対策として Eloquent ORM を使用してください
- 認証が必要なエンドポイントには `security: [['sanctum' => []]]` を記述してください

### Performance

- N+1 問題を避けるため、Eager Loading を使用してください
- 大量のデータを扱う場合は Pagination を使用してください
- インデックスを適切に設定してください

```php
// ✅ Good: Eager Loading
$notes = Note::with('user')->get();

// ❌ Bad: N+1 問題
$notes = Note::all();
foreach ($notes as $note) {
    echo $note->user->name; // 各ループで追加クエリが発生
}
```

## Summary

- ✅ すべてのファイルに `declare(strict_types=1);` を記述
- ✅ 型ヒントを必ず記述（引数・戻り値）
- ✅ レイヤードアーキテクチャに従う（Controller → Action → Model）
- ✅ ビジネスロジックは Action クラスに実装
- ✅ Form Request でバリデーション
- ✅ OpenAPI アトリビュートで API ドキュメント化
- ✅ JSON レスポンスに `JSON_UNESCAPED_UNICODE` を指定
- ✅ Sqid と数値 ID の両方をサポート
- ✅ 論理削除（Soft Deletes）を使用
- ✅ Feature Test を書く
- ✅ エラーメッセージは日本語でわかりやすく

これらの規約に従うことで、コードベースの一貫性と保守性が向上します。
