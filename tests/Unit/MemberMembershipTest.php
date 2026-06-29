<?php

namespace Tests\Unit;

use App\Models\MemberMembership;
use App\Models\Member;
use Tests\TestCase;

class MemberMembershipTest extends TestCase
{
    function test_belongs_to_member()
    {
        $member = Member::factory()->create();
        $membership = MemberMembership::factory()->create(['member_id' => $member->id]);
        $this->assertInstanceOf(Member::class, $membership->member);
        $this->assertEquals($member->id, $membership->member->id);
    }

    function test_scope_active_filters_active_memberships()
    {
        MemberMembership::factory()->count(3)->create(['status' => 'active']);
        MemberMembership::factory()->create(['status' => 'inactive']);
        $this->assertCount(3, MemberMembership::active()->get());
    }
}
