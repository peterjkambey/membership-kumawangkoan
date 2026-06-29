<?php

namespace Tests\Unit;

use App\Models\FamilyCard;
use App\Models\Member;
use App\Models\MonthlyBill;
use Tests\TestCase;

class FamilyCardTest extends TestCase
{
    function test_has_many_members()
    {
        $card = FamilyCard::factory()->create();
        Member::factory()->count(3)->create(['family_card_id' => $card->id]);
        $this->assertCount(3, $card->members);
    }

    function test_has_a_head_member()
    {
        $head = Member::factory()->create();
        $card = FamilyCard::factory()->create(['head_member_id' => $head->id]);
        $this->assertInstanceOf(Member::class, $card->headMember);
        $this->assertEquals($head->id, $card->headMember->id);
    }

    function test_has_many_monthly_bills()
    {
        $card = FamilyCard::factory()
            ->has(MonthlyBill::factory()->count(3))
            ->create();
        $this->assertCount(3, $card->monthlyBills);
    }

    function test_scope_active_filters_active_cards()
    {
        FamilyCard::factory()->count(3)->create(['status' => 'active']);
        FamilyCard::factory()->create(['status' => 'inactive']);
        FamilyCard::factory()->create(['status' => 'frozen']);
        $this->assertCount(3, FamilyCard::active()->get());
    }

    function test_scope_frozen_filters_frozen_cards()
    {
        FamilyCard::factory()->count(2)->create(['status' => 'frozen']);
        FamilyCard::factory()->count(3)->create(['status' => 'active']);
        $this->assertCount(2, FamilyCard::frozen()->get());
    }

    function test_total_members_returns_correct_count()
    {
        $card = FamilyCard::factory()->create();
        Member::factory()->count(4)->create(['family_card_id' => $card->id]);
        $this->assertEquals(4, $card->totalMembers);
    }

    function test_outstanding_bills_returns_sum()
    {
        $card = FamilyCard::factory()->create();
        MonthlyBill::factory()->create(['family_card_id' => $card->id, 'amount' => 100000, 'status' => 'unpaid']);
        MonthlyBill::factory()->create(['family_card_id' => $card->id, 'amount' => 50000, 'status' => 'overdue']);
        MonthlyBill::factory()->create(['family_card_id' => $card->id, 'amount' => 200000, 'status' => 'paid']);
        $this->assertEquals(150000, $card->outstandingBills);
    }

    function test_family_no_must_be_unique()
    {
        FamilyCard::factory()->create(['family_no' => 'KK-001']);
        $this->expectException(\Illuminate\Database\QueryException::class);
        FamilyCard::factory()->create(['family_no' => 'KK-001']);
    }
}
