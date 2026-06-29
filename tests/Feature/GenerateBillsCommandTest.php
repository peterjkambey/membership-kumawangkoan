<?php

namespace Tests\Feature;

use App\Models\FamilyCard;
use App\Models\MonthlyBill;
use Tests\Helpers\CreatesUsers;
use Tests\TestCase;

class GenerateBillsCommandTest extends TestCase
{
    use CreatesUsers;

    function test_command_creates_bills_for_active_cards()
    {
        FamilyCard::factory()->count(3)->create(['status' => 'active', 'monthly_dues' => 20000]);
        FamilyCard::factory()->create(['status' => 'inactive']);

        $this->artisan('bills:generate', ['--period' => '2026-07'])
            ->assertSuccessful();

        $this->assertDatabaseCount('monthly_bills', 3);
    }

    function test_command_uses_monthly_dues_from_card()
    {
        FamilyCard::factory()->create([
            'status' => 'active',
            'monthly_dues' => 50000,
        ]);

        $this->artisan('bills:generate', ['--period' => '2026-07'])
            ->assertSuccessful();

        $this->assertDatabaseHas('monthly_bills', [
            'period' => '2026-07',
            'amount' => '50000',
        ]);
    }

    function test_command_skips_cards_with_existing_bill()
    {
        $card = FamilyCard::factory()->create(['status' => 'active']);
        MonthlyBill::factory()->create([
            'family_card_id' => $card->id,
            'period' => '2026-07',
        ]);

        $this->artisan('bills:generate', ['--period' => '2026-07'])
            ->assertSuccessful();

        // Still only 1 bill (wasn't duplicated)
        $this->assertDatabaseCount('monthly_bills', 1);
    }

    function test_command_dry_run_does_not_insert()
    {
        FamilyCard::factory()->count(3)->create(['status' => 'active']);

        $this->artisan('bills:generate', [
            '--period' => '2026-07',
            '--dry-run' => true,
        ])->assertSuccessful();

        $this->assertDatabaseCount('monthly_bills', 0);
    }

    function test_command_handles_empty_active_cards()
    {
        $this->artisan('bills:generate', ['--period' => '2026-07'])
            ->assertSuccessful();

        $this->assertDatabaseCount('monthly_bills', 0);
    }

    function test_command_sets_correct_due_date()
    {
        FamilyCard::factory()->create(['status' => 'active']);

        $this->artisan('bills:generate', ['--period' => '2026-07'])
            ->assertSuccessful();

        $bill = MonthlyBill::first();
        $this->assertEquals('unpaid', $bill->status);
        $this->assertEquals('2026-07-15', $bill->due_date->format('Y-m-d'));
    }

    function test_command_default_period_is_current_month()
    {
        FamilyCard::factory()->create(['status' => 'active']);

        $this->artisan('bills:generate')
            ->assertSuccessful();

        $expectedPeriod = now()->format('Y-m');
        $this->assertDatabaseHas('monthly_bills', ['period' => $expectedPeriod]);
    }
}
