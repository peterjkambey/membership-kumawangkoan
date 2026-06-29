<?php

namespace Tests\Unit;

use App\Models\Member;
use App\Models\FamilyCard;
use App\Models\Region;
use App\Models\MemberMembership;
use App\Models\MembershipStatusLog;
use App\Models\SupportBody;
use App\Models\MonthlyBill;
use Tests\TestCase;

class MemberTest extends TestCase
{
    function test_member_belongs_to_family_card()
    {
        $familyCard = FamilyCard::factory()->create();
        $member = Member::factory()->create(['family_card_id' => $familyCard->id]);

        $this->assertInstanceOf(FamilyCard::class, $member->familyCard);
        $this->assertEquals($familyCard->id, $member->familyCard->id);
    }

    function test_member_belongs_to_region()
    {
        $region = Region::factory()->create();
        $member = Member::factory()->create(['region_id' => $region->id]);

        $this->assertInstanceOf(Region::class, $member->region);
        $this->assertEquals($region->id, $member->region->id);
    }

    function test_member_has_many_memberships()
    {
        $member = Member::factory()->create();
        MemberMembership::factory()->count(2)->create(['member_id' => $member->id]);

        $this->assertCount(2, $member->memberships);
    }

    function test_member_has_one_active_membership()
    {
        $member = Member::factory()->create();
        MemberMembership::factory()->create(['member_id' => $member->id, 'status' => 'inactive']);
        MemberMembership::factory()->create(['member_id' => $member->id, 'status' => 'active']);

        $this->assertInstanceOf(MemberMembership::class, $member->activeMembership);
        $this->assertEquals('active', $member->activeMembership->status);
    }

    function test_member_has_many_status_logs()
    {
        $member = Member::factory()->create();
        MembershipStatusLog::factory()->count(3)->create(['member_id' => $member->id]);

        $this->assertCount(3, $member->statusLogs);
    }

    function test_member_can_be_head_of_family_cards()
    {
        $member = Member::factory()->create();
        FamilyCard::factory()->count(2)->create(['head_member_id' => $member->id]);

        $this->assertCount(2, $member->headOfFamilyCards);
    }

    function test_member_belongs_to_many_support_bodies()
    {
        $member = Member::factory()->create();
        $bodies = SupportBody::factory()->count(3)->create();
        $member->supportBodies()->attach($bodies->pluck('id'));

        $this->assertCount(3, $member->supportBodies);
    }

    function test_scope_active_filters_active_members()
    {
        Member::factory()->count(3)->create(['status' => 'active']);
        Member::factory()->create(['status' => 'inactive']);
        Member::factory()->create(['status' => 'deceased']);

        $this->assertCount(3, Member::active()->get());
    }

    function test_scope_by_region_filters_members_by_region()
    {
        $region = Region::factory()->create();
        Member::factory()->count(2)->create(['region_id' => $region->id]);
        Member::factory()->count(3)->create();

        $this->assertCount(2, Member::byRegion($region->id)->get());
    }

    function test_age_is_null_when_no_birth_date()
    {
        $member = Member::factory()->create(['birth_date' => null]);

        $this->assertNull($member->age);
    }

    function test_family_role_label_returns_correct_indonesian_label()
    {
        $this->assertEquals('Kepala Keluarga', (new Member(['family_role' => 'head']))->familyRoleLabel);
        $this->assertEquals('Pasangan', (new Member(['family_role' => 'spouse']))->familyRoleLabel);
        $this->assertEquals('Anak', (new Member(['family_role' => 'child']))->familyRoleLabel);
        $this->assertEquals('Orang Tua', (new Member(['family_role' => 'parent']))->familyRoleLabel);
        $this->assertEquals('Saudara', (new Member(['family_role' => 'sibling']))->familyRoleLabel);
        $this->assertEquals('Lainnya', (new Member(['family_role' => 'other']))->familyRoleLabel);
    }

    function test_membership_number_can_be_nullable()
    {
        $member = Member::factory()->create(['membership_number' => null]);

        $this->assertNull($member->membership_number);
    }
}
