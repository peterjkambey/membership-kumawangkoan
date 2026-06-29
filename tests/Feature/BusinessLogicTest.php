<?php

namespace Tests\Feature;

use App\Models\FamilyCard;
use App\Models\Member;
use App\Models\MemberMembership;
use App\Models\MonthlyBill;
use App\Models\Payment;
use App\Models\Region;
use App\Models\SupportBody;
use App\Models\User;
use Tests\Helpers\CreatesUsers;
use Tests\TestCase;

class BusinessLogicTest extends TestCase
{
    use CreatesUsers;

    /*
    |--------------------------------------------------------------------------
    | Payment & MonthlyBill Lifecycle
    |--------------------------------------------------------------------------
    */

    /** @test */
    function test_full_payment_flow_makes_bill_fully_paid()
    {
        $card = FamilyCard::factory()->create();
        $bill = MonthlyBill::factory()->create([
            'family_card_id' => $card->id,
            'amount' => 200000,
            'status' => 'unpaid',
        ]);

        // Partial payment -> remaining masih ada
        Payment::factory()->create([
            'monthly_bill_id' => $bill->id,
            'amount' => 150000,
        ]);
        $this->assertEquals(50000, $bill->fresh()->remaining);
        $this->assertFalse($bill->fresh()->isFullyPaid);

        // Full payment -> remaining 0
        Payment::factory()->create([
            'monthly_bill_id' => $bill->id,
            'amount' => 50000,
        ]);
        $this->assertEquals(0, $bill->fresh()->remaining);
        $this->assertTrue($bill->fresh()->isFullyPaid);
    }

    /** @test */
    function test_payment_can_be_verified_by_admin()
    {
        $admin = $this->createSuperAdmin();
        $bill = MonthlyBill::factory()->create(['status' => 'unpaid']);
        $payment = Payment::factory()->create([
            'monthly_bill_id' => $bill->id,
            'verified_by' => $admin->id,
        ]);

        $this->assertInstanceOf(User::class, $payment->verifiedBy);
        $this->assertEquals($admin->id, $payment->verifiedBy->id);
    }

    /** @test */
    function test_multiple_payments_on_same_bill()
    {
        $card = FamilyCard::factory()->create();
        $bill = MonthlyBill::factory()->create([
            'family_card_id' => $card->id,
            'amount' => 300000,
        ]);

        Payment::factory()->count(3)->create([
            'monthly_bill_id' => $bill->id,
            'amount' => 100000,
        ]);

        $this->assertEquals(300000, $bill->fresh()->totalPaid);
        $this->assertTrue($bill->fresh()->isFullyPaid);
        $this->assertCount(3, $bill->fresh()->payments);
    }

    /** @test */
    function test_overdue_bill_detection()
    {
        MonthlyBill::factory()->create([
            'status' => 'overdue',
            'due_date' => now()->subDays(5),
        ]);

        $overdueBills = MonthlyBill::where('status', 'overdue')->get();
        $this->assertCount(1, $overdueBills);
    }

    /*
    |--------------------------------------------------------------------------
    | FamilyCard & Member Lifecycle
    |--------------------------------------------------------------------------
    */

    /** @test */
    function test_family_card_with_full_members()
    {
        $card = FamilyCard::factory()->create();
        $head = Member::factory()->head()->create([
            'family_card_id' => $card->id,
            'family_role' => 'head',
        ]);
        // Set as head of family
        $card->head_member_id = $head->id;
        $card->save();

        Member::factory()->count(2)->create([
            'family_card_id' => $card->id,
            'family_role' => 'child',
        ]);
        Member::factory()->create([
            'family_card_id' => $card->id,
            'family_role' => 'spouse',
        ]);

        $this->assertCount(4, $card->fresh()->members);
        $this->assertEquals($head->id, $card->fresh()->headMember->id);
    }

    /** @test */
    function test_region_member_count()
    {
        $region = Region::factory()->create();
        Member::factory()->count(5)->create(['region_id' => $region->id]);
        Member::factory()->count(3)->create();

        $this->assertCount(5, $region->fresh()->members);
    }

    /** @test */
    function test_unique_constraints_on_member_data()
    {
        Member::factory()->create(['nik' => '1234567890123456']);

        $this->expectException(\Illuminate\Database\QueryException::class);
        Member::factory()->create(['nik' => '1234567890123456']);
    }

    /*
    |--------------------------------------------------------------------------
    | SupportBody Member Assignment
    |--------------------------------------------------------------------------
    */

    /** @test */
    function test_member_can_be_assigned_to_multiple_support_bodies()
    {
        $member = Member::factory()->create();
        $bodies = SupportBody::factory()->count(3)->create();
        $member->supportBodies()->attach($bodies->pluck('id'));

        $this->assertCount(3, $member->fresh()->supportBodies);
    }

    /** @test */
    function test_support_body_can_have_multiple_members()
    {
        $body = SupportBody::factory()->create();
        $members = Member::factory()->count(4)->create();
        $body->members()->attach($members->pluck('id'));

        $this->assertCount(4, $body->fresh()->members);
    }

    /** @test */
    function test_support_body_member_sync_replaces_assignments()
    {
        $body = SupportBody::factory()->create();
        $oldMembers = Member::factory()->count(2)->create();
        $body->members()->attach($oldMembers->pluck('id'));

        $newMembers = Member::factory()->count(3)->create();
        $body->members()->sync($newMembers->pluck('id'));

        $this->assertCount(3, $body->fresh()->members);
    }

    /*
    |--------------------------------------------------------------------------
    | Member Membership Status
    |--------------------------------------------------------------------------
    */

    /** @test */
    function test_member_can_have_multiple_membership_periods()
    {
        $member = Member::factory()->create();

        MemberMembership::factory()->create([
            'member_id' => $member->id,
            'status' => 'active',
            'start_date' => '2026-01-01',
        ]);

        // Deactivate and create new membership
        $member->activeMembership->update(['status' => 'inactive']);
        MemberMembership::factory()->create([
            'member_id' => $member->id,
            'status' => 'active',
            'start_date' => '2026-06-01',
        ]);

        $this->assertCount(2, $member->fresh()->memberships);
        $this->assertTrue($member->fresh()->activeMembership->exists());
    }

    /*
    |--------------------------------------------------------------------------
    | File Upload (via Storage Fake)
    |--------------------------------------------------------------------------
    */

    /** @test */
    function test_member_photo_path_is_stored()
    {
        $member = Member::factory()->create(['photo' => 'member-photos/test-photo.jpg']);
        $this->assertNotNull($member->photo);
        $this->assertStringContainsString('member-photos', $member->photo);
    }
}
