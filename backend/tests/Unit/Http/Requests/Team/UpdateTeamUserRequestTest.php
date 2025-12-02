<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Requests\Team;

use App\Http\Requests\Team\UpdateTeamUserRequest;
use Tests\TestCase;

class UpdateTeamUserRequestTest extends TestCase
{
    public function test_it_authorizes_authenticated_users(): void
    {
        $request = new UpdateTeamUserRequest;

        $this->assertTrue($request->authorize());
    }

    public function test_it_has_is_owner_validation_rules(): void
    {
        $request = new UpdateTeamUserRequest;

        $rules = $request->rules();

        $this->assertArrayHasKey('is_owner', $rules);
        $this->assertContains('required', $rules['is_owner']);
        $this->assertContains('boolean', $rules['is_owner']);
    }

    public function test_it_has_all_required_validation_rules(): void
    {
        $request = new UpdateTeamUserRequest;

        $rules = $request->rules();

        $this->assertCount(1, $rules);
        $this->assertArrayHasKey('is_owner', $rules);
    }
}
