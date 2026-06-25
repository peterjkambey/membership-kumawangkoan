<?php

namespace App\Console\Commands;

use App\Models\FamilyCard;
use App\Models\MonthlyBill;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateMonthlyBills extends Command
{
    protected $signature = 'membership:generate-bills {--period= : Periode dalam format YYYY-MM}';

    protected $description = 'Generate monthly bills for all active family cards';

    public function handle(): int
    {
        $period = $this->option('period') ?? Carbon::now()->format('Y-m');
        $dueDate = Carbon::createFromFormat('Y-m', $period)->endOfMonth();
        $amount = 50000; // Default iuran, bisa dikonfigurasi

        $familyCards = FamilyCard::where('status', 'active')->get();

        $generated = 0;
        $skipped = 0;

        foreach ($familyCards as $card) {
            $exists = MonthlyBill::where('family_card_id', $card->id)
                ->where('period', $period)
                ->exists();

            if ($exists) {
                $skipped++;
                continue;
            }

            MonthlyBill::create([
                'family_card_id' => $card->id,
                'period' => $period,
                'amount' => $amount,
                'status' => 'unpaid',
                'due_date' => $dueDate,
            ]);

            $generated++;
        }

        $this->info("Periode: {$period}");
        $this->info("Tagihan dibuat: {$generated}");
        $this->info("Sudah ada (dilewati): {$skipped}");

        return Command::SUCCESS;
    }
}
