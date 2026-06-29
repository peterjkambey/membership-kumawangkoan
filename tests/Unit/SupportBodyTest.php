<?php

namespace Tests\Unit;

use App\Models\SupportBody;
use App\Models\Member;
use Tests\TestCase;

class SupportBodyTest extends TestCase
{
    function test_belongs_to_many_members()
    {
        $body = SupportBody::factory()->create();
        $members = Member::factory()->count(3)->create();
        $body->members()->attach($members->pluck('id'));
        $this->assertCount(3, $body->members);
    }
}
