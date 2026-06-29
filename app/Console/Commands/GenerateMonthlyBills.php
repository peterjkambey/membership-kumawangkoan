<?php

namespace App\Console\Commands;

use App\Models\FamilyCard;
use App\Models\MonthlyBill;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateMonthlyBills extends Command
{
    protected $signature = 'bills:generate
        {--period= : Periode dalam format YYYY-MM (default: bulan sekarang)}
        {--dry-run : Jalankan simulasi tanpa insert}';

    protected $description = 'Generate tagihan bulanan untuk semua KK aktif';

    public function handle(): int
    {
        $period = $this->option('period') ?? now()->format('Y-m');
        $dryRun = $this->option('dry-run');
        $periodDate = \Carbon\Carbon::createFromFormat('Y-m', $period);
        $dueDate = $periodDate->copy()->day(15);
        $created = 0;
        $skipped = 0;

        $cards = FamilyCard::with('headMember')
            ->where('status', 'active')
            ->get();

        if ($cards->isEmpty()) {
            $this->warn('Tidak ada KK aktif ditemukan.');
            return Command::SUCCESS;
        }

        $this->info("Memproses {$cards->count()} KK aktif untuk periode {$period}...");

        foreach ($cards as $card) {
            $exists = MonthlyBill::where('family_card_id', $card->id)
                ->where('period', $period)
                ->exists();

            if ($exists) {
                $skipped++;
                continue;
            }

            $amount = $card->monthly_dues ?? 20000;

            if (!$dryRun) {
                MonthlyBill::create([
                    'family_card_id' => $card->id,
                    'period' => $period,
                    'amount' => $amount,
                    'status' => 'unpaid',
                    'due_date' => $dueDate,
                ]);
            }

            $created++;
            $headName = $card->headMember?->full_name ?? '—';
            $this->line("  [{$card->family_no}] {$headName} — Rp " . number_format($amount, 0, ',', '.'));
        }

        $this->newLine();
        $this->table(
            ['Status', 'Jumlah'],
            [
                ['KK Aktif', $cards->count()],
                ['Tagihan Dibuat', $created],
                ['Sudah Ada (skip)', $skipped],
                ['Total', $cards->count()],
            ]
        );

        if ($dryRun) {
            $this->info('☑ Dry-run — tidak ada data yang diinsert.');
        }

        return Command::SUCCESS;
    }
}
