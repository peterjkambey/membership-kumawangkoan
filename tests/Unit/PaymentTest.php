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
}