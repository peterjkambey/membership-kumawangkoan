<?php

namespace App\Console\Commands;

use App\Models\MonthlyBill;
use App\Models\MembershipStatusLog;
use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckOverdueBills extends Command
{
    protected $signature = 'membership:check-overdue';

    protected $description = 'Check overdue bills and apply freezing rules';

    public function handle(): int
    {
        $today = Carbon::now();

        // Mark bills as overdue if due date has passed and still unpaid
        $overdueBills = MonthlyBill::where('status', 'unpaid')
            ->where('due_date', '<', $today)
            ->update(['status' => 'overdue']);

        $this->info("Tagihan terlambat: {$overdueBills}");

        // Freezing rules:
        // 0 bulan = aktif
        // 1 bulan telat = warning
        // 2 bulan telat = warning kedua
        // 3 bulan telat = frozen

        $threeMonthsAgo = $today->copy()->subMonths(3);

        $familyCardsToFreeze = \App\Models\FamilyCard::where('status', 'active')
            ->whereHas('monthlyBills', function ($query) use ($threeMonthsAgo) {
                $query->where('status', 'overdue')
                    ->where('due_date', '<', $threeMonthsAgo);
            }, '>=', 3)
            ->get();

        foreach ($familyCardsToFreeze as $card) {
            $card->update(['status' => 'frozen']);

            // Log status change for each member
            foreach ($card->members as $member) {
                MembershipStatusLog::create([
                    'member_id' => $member->id,
                    'previous_status' => $member->status,
                    'new_status' => 'frozen',
                    'reason' => 'Otomatis: tunggakan iuran lebih dari 3 bulan',
                ]);

                // Also freeze the member's active membership
                $activeMembership = $member->activeMembership;
                if ($activeMembership) {
                    $activeMembership->update(['status' => 'suspended']);
                }
            }
        }

        $this->info("KK dibekukan: {$familyCardsToFreeze->count()}");

        return Command::SUCCESS;
    }
}
