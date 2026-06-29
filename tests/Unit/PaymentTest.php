<?php

namespace Tests\Unit;

use App\Models\FamilyCard;
use App\Models\Payment;
use App\Models\MonthlyBill;
use App\Models\User;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    function test_belongs_to_monthly_bill()
    {
        $bill = MonthlyBill::factory()->create();
        $payment = Payment::factory()->create(['monthly_bill_id' => $bill->id]);
        $this->assertInstanceOf(MonthlyBill::class, $payment->monthlyBill);
        $this->assertEquals($bill->id, $payment->monthlyBill->id);
    }

    function test_can_be_verified_by_user()
    {
        $user = User::factory()->create();
        $payment = Payment::factory()->verified()->create(['verified_by' => $user->id]);
        $this->assertInstanceOf(User::class, $payment->verifiedBy);
        $this->assertEquals($user->id, $payment->verifiedBy->id);
    }

    function test_verified_by_can_be_null()
    {
        $payment = Payment::factory()->create(['verified_by' => null]);

        $this->assertNull($payment->verifiedBy);
    }

    function test_can_belong_to_family_card()
    {
        $card = FamilyCard::factory()->create();
        $payment = Payment::factory()->create(['family_card_id' => $card->id]);

        $this->assertInstanceOf(FamilyCard::class, $payment->familyCard);
        $this->assertEquals($card->id, $payment->familyCard->id);
    }

    function test_reference_number_auto_generated()
    {
        $payment = Payment::factory()->create(['reference_number' => null]);

        $this->assertNotNull($payment->reference_number);
        $this->assertStringStartsWith('PAY/', $payment->reference_number);
        $this->assertMatchesRegularExpression('/^PAY\/\d{8}\/\d{4}$/', $payment->reference_number);
    }

    function test_reference_number_increments_per_day()
    {
        $p1 = Payment::factory()->create(['reference_number' => null, 'payment_date' => '2026-07-01']);
        $p2 = Payment::factory()->create(['reference_number' => null, 'payment_date' => '2026-07-01']);

        $this->assertEquals('PAY/20260701/0001', $p1->reference_number);
        $this->assertEquals('PAY/20260701/0002', $p2->reference_number);
    }

    function test_reference_number_can_be_manual()
    {
        $payment = Payment::factory()->create(['reference_number' => 'MANUAL-001']);

        $this->assertEquals('MANUAL-001', $payment->reference_number);
    }
}