<?php

namespace Tests\Unit;

use App\Models\MonthlyBill;
use App\Models\FamilyCard;
use App\Models\Payment;
use Tests\TestCase;

class MonthlyBillTest extends TestCase
{
    function test_belongs_to_a_family_card()
    {
        $card = FamilyCard::factory()->create();
        $bill = MonthlyBill::factory()->create(['family_card_id' => $card->id]);
        $this->assertInstanceOf(FamilyCard::class, $bill->familyCard);
        $this->assertEquals($card->id, $bill->familyCard->id);
    }

    function test_has_many_payments()
    {
        $bill = MonthlyBill::factory()->has(Payment::factory()->count(2))->create();
        $this->assertCount(2, $bill->payments);
    }

    function test_scope_unpaid_filters_unpaid_bills()
    {
        MonthlyBill::factory()->count(2)->create(['status' => 'unpaid']);
        MonthlyBill::factory()->create(['status' => 'paid']);
        MonthlyBill::factory()->create(['status' => 'overdue']);
        $this->assertCount(3, MonthlyBill::unpaid()->get());
    }

    function test_scope_by_period_filters_by_period()
    {
        MonthlyBill::factory()->count(2)->create(['period' => '2026-06']);
        MonthlyBill::factory()->count(3)->create(['period' => '2026-07']);
        $this->assertCount(2, MonthlyBill::byPeriod('2026-06')->get());
    }

    function test_total_paid_returns_sum_of_all_payments()
    {
        $bill = MonthlyBill::factory()->create(['amount' => 200000]);
        Payment::factory()->create(['monthly_bill_id' => $bill->id, 'amount' => 100000]);
        Payment::factory()->create(['monthly_bill_id' => $bill->id, 'amount' => 50000]);
        $this->assertEquals(150000, $bill->totalPaid);
    }

    function test_remaining_returns_outstanding_amount()
    {
        $bill = MonthlyBill::factory()->create(['amount' => 200000]);
        Payment::factory()->create(['monthly_bill_id' => $bill->id, 'amount' => 80000]);
        $this->assertEquals(120000, $bill->remaining);
    }

    function test_remaining_is_zero_when_fully_paid()
    {
        $bill = MonthlyBill::factory()->create(['amount' => 200000]);
        Payment::factory()->create(['monthly_bill_id' => $bill->id, 'amount' => 200000]);
        $this->assertEquals(0, $bill->remaining);
    }

    function test_is_fully_paid_returns_true_when_paid_in_full()
    {
        $bill = MonthlyBill::factory()->create(['amount' => 200000]);
        $this->assertFalse($bill->isFullyPaid);
        Payment::factory()->create(['monthly_bill_id' => $bill->id, 'amount' => 200000]);
        $this->assertTrue($bill->fresh()->isFullyPaid);
    }

    function test_family_card_and_period_must_be_unique()
    {
        $card = FamilyCard::factory()->create();
        MonthlyBill::factory()->create(['family_card_id' => $card->id, 'period' => '2026-06']);
        $this->expectException(\Illuminate\Database\QueryException::class);
        MonthlyBill::factory()->create(['family_card_id' => $card->id, 'period' => '2026-06']);
    }
}
