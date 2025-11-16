# Laravel PHPUnit Testing Best Practices

## 1. Overview

Laravel uses PHPUnit as its default testing framework, and tests are placed under the `tests/` directory.
They are generally categorized into the following three layers:

| Type              | Purpose                                                                  | Directory                     |
| ----------------- | ------------------------------------------------------------------------ | ----------------------------- |
| Unit Tests        | Validate individual logic (models, services, use cases)                  | `tests/Unit/`                 |
| Feature Tests     | Verify application behavior including routes and controllers             | `tests/Feature/`              |
| Integration Tests | (Optional) Validate combined behavior with external APIs or multiple DBs | `tests/Integration/` (manual) |

---

## 2. Naming Conventions

| Target     | File Name                 | Class Name            | Method Name                  |
| ---------- | ------------------------- | --------------------- | ---------------------------- |
| Model      | `UserTest.php`            | `UserTest`            | `it_creates_a_user`          |
| Controller | `UserControllerTest.php`  | `UserControllerTest`  | `it_returns_user_list`       |
| Service    | `SendMailServiceTest.php` | `SendMailServiceTest` | `it_sends_mail_successfully` |

**Rules:**

-   Start method names with `it_` to describe expected behavior (e.g., `it_creates_a_new_user_record`)
-   Use snake_case for test method names
-   Each `it_` test should have only one clear expectation

---

## 3. Structure and Execution

### 3.1 Directory Structure

```bash
tests/
├── Feature/
│   ├── Http/
│   │   └── Controllers/
│   │       └── Api/
│   │           └── V1/
│   │               └── UserControllerTest.php
│   └── Middleware/
├── Unit/
│   ├── Models/
│   │   └── UserTest.php
│   ├── Services/
│   │   └── SendMailServiceTest.php
│   └── UseCases/
└── TestCase.php
```

### 3.2 Commands

```bash
# Run all tests
php artisan test

# Run a specific test file
php artisan test --filter=UserControllerTest

# Show detailed output
php artisan test -v
```

---

## 4. Test Writing Principles

### 4.1 Arrange–Act–Assert Pattern

Follow the **AAA pattern** for clarity: Arrange (setup), Act (execute), Assert (verify).

```php
public function test_it_creates_a_user()
{
    // Arrange: Prepare data
    $data = ['name' => 'Taro', 'email' => 'taro@example.com'];

    // Act: Execute request
    $response = $this->postJson('/api/v1/users', $data);

    // Assert: Verify result
    $response->assertCreated();
    $this->assertDatabaseHas('users', ['email' => 'taro@example.com']);
}
```

---

## 5. Database Testing Best Practices

### 5.1 Reset Database State

Use the `RefreshDatabase` trait to reset the DB for each test.

```php
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;
}
```

### 5.2 Using Factories

Leverage Laravel model factories (available since v8) to generate data efficiently.

```php
$user = User::factory()->create([
    'email' => 'taro@example.com',
]);
```

---

## 6. HTTP Test Techniques

-   Use methods like `getJson`, `postJson`, `putJson`, `deleteJson`
-   Use assertions like `assertStatus`, `assertJson`, `assertExactJson`, `assertJsonFragment`

```php
$response = $this->getJson('/api/v1/users');
$response->assertStatus(200)
         ->assertJsonStructure(['data' => [['id', 'name', 'email']]]);
```

---

## 7. Mocking and Stubbing

Use **Mockery** or Laravel’s dependency injection mock helpers to isolate external dependencies.

```php
$this->mock(SendMailService::class, function ($mock) {
    $mock->shouldReceive('send')->once()->andReturn(true);
});
```

---

## 8. CI/CD Integration Example

Automate test execution with GitHub Actions by adding the following workflow file.

```yaml
name: CI
on: [push, pull_request]

jobs:
    test:
        runs-on: ubuntu-latest
        steps:
            - uses: actions/checkout@v4
            - uses: shivammathur/setup-php@v2
              with:
                  php-version: "8.3"
            - run: composer install
            - run: php artisan test --stop-on-failure
```

---

## 9. Code Coverage

Add logging to `phpunit.xml` for coverage reports.

```xml
<logging>
  <log type="coverage-html" target="storage/coverage" />
</logging>
```

Run the coverage command:

```bash
vendor/bin/phpunit --coverage-html storage/coverage
```

---

## 10. Quality Standards

| Metric        | Recommended Value                                               |
| ------------- | --------------------------------------------------------------- |
| Code Coverage | ≥ 80%                                                           |
| Test Runtime  | ≤ 60 seconds                                                    |
| Factory Usage | Prefer 1 record per test                                        |
| Naming        | Descriptive natural language (`it_creates_user_on_valid_input`) |
| Structure     | Must follow Arrange–Act–Assert                                  |

---

## 11. Recommended Guidelines

-   Feature tests should emphasize **request–response consistency**.
-   Unit tests should focus on **side-effect logic** (no HTTP or DB calls).
-   Always include **error and exception cases** (`assertThrows`, `expectException`).
-   Keep each test self-contained and readable.
