<?php

namespace Tests\Unit;

use App\Models\MembershipStatusLog;
use App\Models\Member;
use App\Models\User;
use Tests\TestCase;

class MembershipStatusLogTest extends TestCase
{
    function test_belongs_to_member()
    {
        $member = Member::factory()->create();
        $log = MembershipStatusLog::factory()->create(['member_id' => $member->id]);
        $this->assertInstanceOf(Member::class, $log->member);
        $this->assertEquals($member->id, $log->member->id);
    }

    function test_has_changed_by_user()
    {
        $user = User::factory()->create();
        $log = MembershipStatusLog::factory()->create(['changed_by' => $user->id]);
        $this->assertInstanceOf(User::class, $log->changedBy);
        $this->assertEquals($user->id, $log->changedBy->id);
    }
}
