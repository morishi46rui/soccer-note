<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Requests\Team;

use App\Http\Requests\Team\CreateTeamRequest;
use Tests\TestCase;

class CreateTeamRequestTest extends TestCase
{
    public function test_it_authorizes_authenticated_users(): void
    {
        $request = new CreateTeamRequest;

        $this->assertTrue($request->authorize());
    }

    public function test_it_has_name_validation_rules(): void
    {
        $request = new CreateTeamRequest;

        $rules = $request->rules();

        $this->assertArrayHasKey('name', $rules);
        $this->assertContains('required', $rules['name']);
        $this->assertContains('string', $rules['name']);
        $this->assertContains('max:255', $rules['name']);
    }

    public function test_it_has_description_validation_rules(): void
    {
        $request = new CreateTeamRequest;

        $rules = $request->rules();

        $this->assertArrayHasKey('description', $rules);
        $this->assertContains('nullable', $rules['description']);
        $this->assertContains('string', $rules['description']);
    }

    public function test_it_has_all_required_validation_rules(): void
    {
        $request = new CreateTeamRequest;

        $rules = $request->rules();

        $this->assertCount(2, $rules);
        $this->assertArrayHasKey('name', $rules);
        $this->assertArrayHasKey('description', $rules);
    }
}
