<?php

namespace Tests\Feature;

use App\Models\FamilyCard;
use App\Models\MonthlyBill;
use App\Models\Payment;
use App\Services\PaymentAllocator;
use Tests\TestCase;

class PaymentAllocationTest extends TestCase
{
    private PaymentAllocator $allocator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->allocator = app(PaymentAllocator::class);
    }

    function test_allocates_payment_to_oldest_bill_first()
    {
        $card = FamilyCard::factory()->create(['monthly_dues' => 20000]);

        // 3 bulan tunggakan
        MonthlyBill::factory()->create(['family_card_id' => $card->id, 'period' => '2026-04', 'amount' => 20000, 'status' => 'unpaid']);
        MonthlyBill::factory()->create(['family_card_id' => $card->id, 'period' => '2026-05', 'amount' => 20000, 'status' => 'unpaid']);
        MonthlyBill::factory()->create(['family_card_id' => $card->id, 'period' => '2026-06', 'amount' => 20000, 'status' => 'unpaid']);

        // Bayar Rp 40.000 = 2 bulan
        $result = $this->allocator->allocate($card, 40000, ['payment_date' => '2026-06-15', 'payment_method' => 'transfer']);

        $this->assertEquals(2, $result['allocated_bills']);
        $this->assertEquals(0, $result['remaining']);

        // April & May lunas, June masih unpaid
        $this->assertEquals('paid', MonthlyBill::where('period', '2026-04')->first()->status);
        $this->assertEquals('paid', MonthlyBill::where('period', '2026-05')->first()->status);
        $this->assertEquals('unpaid', MonthlyBill::where('period', '2026-06')->first()->status);
    }

    function test_allocates_partial_payment_to_first_bill()
    {
        $card = FamilyCard::factory()->create(['monthly_dues' => 20000]);
        MonthlyBill::factory()->create(['family_card_id' => $card->id, 'period' => '2026-06', 'amount' => 20000, 'status' => 'unpaid']);

        // Bayar cuma Rp 10.000 (setengah)
        $result = $this->allocator->allocate($card, 10000, ['payment_date' => '2026-06-15', 'payment_method' => 'cash']);

        $this->assertEquals(1, $result['allocated_bills']);
        $this->assertEquals(0, $result['remaining']);

        // Bill masih unpaid karena belum full
        $bill = MonthlyBill::where('period', '2026-06')->first();
        $this->assertEquals('unpaid', $bill->status);
        $this->assertEquals(10000, $bill->fresh()->remaining);
    }

    function test_full_payment_covers_exact_amount()
    {
        $card = FamilyCard::factory()->create(['monthly_dues' => 50000]);
        MonthlyBill::factory()->create(['family_card_id' => $card->id, 'period' => '2026-06', 'amount' => 50000, 'status' => 'unpaid']);

        $result = $this->allocator->allocate($card, 50000, ['payment_date' => '2026-06-20', 'payment_method' => 'transfer']);

        $this->assertEquals(1, $result['allocated_bills']);
        $this->assertEquals('paid', MonthlyBill::where('period', '2026-06')->first()->status);
        $this->assertTrue(MonthlyBill::where('period', '2026-06')->first()->isFullyPaid);
    }

    function test_excess_payment_is_returned_as_remaining()
    {
        $card = FamilyCard::factory()->create(['monthly_dues' => 20000]);
        MonthlyBill::factory()->create(['family_card_id' => $card->id, 'period' => '2026-06', 'amount' => 20000, 'status' => 'unpaid']);

        // Bayar Rp 50.000 — hanya 20.000 diperlukan
        $result = $this->allocator->allocate($card, 50000, ['payment_date' => '2026-06-15', 'payment_method' => 'transfer']);

        $this->assertEquals(1, $result['allocated_bills']);
        $this->assertEquals(30000, $result['remaining']);
    }

    function test_no_bills_returns_excess_amount()
    {
        $card = FamilyCard::factory()->create();

        $result = $this->allocator->allocate($card, 50000, ['payment_date' => '2026-06-15', 'payment_method' => 'transfer']);

        $this->assertEquals(0, $result['allocated_bills']);
        $this->assertEquals(50000, $result['excess']);
    }

    function test_overdue_bills_are_included_in_allocation()
    {
        $card = FamilyCard::factory()->create(['monthly_dues' => 20000]);
        MonthlyBill::factory()->create(['family_card_id' => $card->id, 'period' => '2026-05', 'amount' => 20000, 'status' => 'overdue']);
        MonthlyBill::factory()->create(['family_card_id' => $card->id, 'period' => '2026-06', 'amount' => 20000, 'status' => 'unpaid']);

        $result = $this->allocator->allocate($card, 40000, ['payment_date' => '2026-06-15', 'payment_method' => 'transfer']);

        $this->assertEquals(2, $result['allocated_bills']);
        $this->assertEquals('paid', MonthlyBill::where('period', '2026-05')->first()->status);
        $this->assertEquals('paid', MonthlyBill::where('period', '2026-06')->first()->status);
    }

    function test_allocation_creates_payment_records()
    {
        $card = FamilyCard::factory()->create(['monthly_dues' => 20000]);
        MonthlyBill::factory()->create(['family_card_id' => $card->id, 'period' => '2026-06', 'amount' => 20000, 'status' => 'unpaid']);
        MonthlyBill::factory()->create(['family_card_id' => $card->id, 'period' => '2026-07', 'amount' => 20000, 'status' => 'unpaid']);

        $this->allocator->allocate($card, 40000, ['payment_date' => '2026-07-10', 'payment_method' => 'qris']);

        $this->assertDatabaseCount('payments', 2);
        $this->assertDatabaseHas('payments', [
            'family_card_id' => $card->id,
            'payment_method' => 'qris',
        ]);
    }
}
