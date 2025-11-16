<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Requests\Group;

use App\Http\Requests\Group\UpdateGroupRequest;
use Tests\TestCase;

class UpdateGroupRequestTest extends TestCase
{
    public function test_it_authorizes_authenticated_users(): void
    {
        $request = new UpdateGroupRequest;

        $this->assertTrue($request->authorize());
    }

    public function test_it_has_team_id_validation_rules(): void
    {
        $request = new UpdateGroupRequest;

        $rules = $request->rules();

        $this->assertArrayHasKey('team_id', $rules);
        $this->assertContains('sometimes', $rules['team_id']);
        $this->assertContains('required', $rules['team_id']);
        $this->assertContains('integer', $rules['team_id']);
        $this->assertContains('exists:teams,id', $rules['team_id']);
    }

    public function test_it_has_name_validation_rules(): void
    {
        $request = new UpdateGroupRequest;

        $rules = $request->rules();

        $this->assertArrayHasKey('name', $rules);
        $this->assertContains('sometimes', $rules['name']);
        $this->assertContains('required', $rules['name']);
        $this->assertContains('string', $rules['name']);
        $this->assertContains('max:255', $rules['name']);
    }

    public function test_it_has_description_validation_rules(): void
    {
        $request = new UpdateGroupRequest;

        $rules = $request->rules();

        $this->assertArrayHasKey('description', $rules);
        $this->assertContains('nullable', $rules['description']);
        $this->assertContains('string', $rules['description']);
    }

    public function test_it_has_all_required_validation_rules(): void
    {
        $request = new UpdateGroupRequest;

        $rules = $request->rules();

        $this->assertCount(3, $rules);
        $this->assertArrayHasKey('team_id', $rules);
        $this->assertArrayHasKey('name', $rules);
        $this->assertArrayHasKey('description', $rules);
    }
}
