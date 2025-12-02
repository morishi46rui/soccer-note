<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Requests\Team;

use App\Http\Requests\Team\AddUserToTeamRequest;
use Tests\TestCase;

class AddUserToTeamRequestTest extends TestCase
{
    public function test_it_authorizes_authenticated_users(): void
    {
        $request = new AddUserToTeamRequest;

        $this->assertTrue($request->authorize());
    }

    public function test_it_has_email_validation_rules(): void
    {
        $request = new AddUserToTeamRequest;

        $rules = $request->rules();

        $this->assertArrayHasKey('email', $rules);
        $this->assertContains('required', $rules['email']);
        $this->assertContains('email', $rules['email']);
        $this->assertContains('exists:users,email', $rules['email']);
    }

    public function test_it_has_is_owner_validation_rules(): void
    {
        $request = new AddUserToTeamRequest;

        $rules = $request->rules();

        $this->assertArrayHasKey('is_owner', $rules);
        $this->assertContains('nullable', $rules['is_owner']);
        $this->assertContains('boolean', $rules['is_owner']);
    }

    public function test_it_has_all_required_validation_rules(): void
    {
        $request = new AddUserToTeamRequest;

        $rules = $request->rules();

        $this->assertCount(2, $rules);
        $this->assertArrayHasKey('email', $rules);
        $this->assertArrayHasKey('is_owner', $rules);
    }
}
