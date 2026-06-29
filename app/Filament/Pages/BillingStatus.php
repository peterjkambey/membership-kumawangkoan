<?php

namespace App\Filament\Pages;

use App\Models\FamilyCard;
use App\Models\MonthlyBill;
use App\Models\Payment;
use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

class BillingStatus extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Status Tagihan';

    protected static ?string $title = 'Status Tagihan per Kartu Keluarga';

    protected static string|\UnitEnum|null $navigationGroup = 'Iuran';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.billing-status';

    public function table(Table $table): Table
    {
        $user = auth()->user();
        $isSuperAdmin = $user && $user->hasRole('super-admin');

        // Determine which periods to show (past 13 months + current)
        $periods = $this->getPeriods();

        return $table
            ->query(
                FamilyCard::query()
                    ->with(['monthlyBills' => fn ($q) => $q->whereIn('period', $periods)])
                    ->withWhereHas('members', fn ($q) => $q->where('family_role', 'head'))
                    ->when(
                        !$isSuperAdmin && $user?->region_id,
                        fn ($q) => $q->whereHas('members', fn ($qq) => $qq->where('region_id', $user->region_id))
                    )
            )
            ->columns([
                TextColumn::make('family_no')
                    ->label('No. KK')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('headMember.full_name')
                    ->label('Kepala Keluarga')
                    ->searchable(),

                TextColumn::make('members_count')
                    ->label('Anggota')
                    ->counts('members'),

                // Outstanding balance (carry-over + current unpaid)
                TextColumn::make('outstanding')
                    ->label('Tunggakan')
                    ->money('IDR')
                    ->getStateUsing(fn (FamilyCard $record) => $this->calculateOutstanding($record)),

                // Dynamically add a column per period
                ...$this->getPeriodColumns($periods),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status KK')
                    ->options([
                        'active' => 'Aktif',
                        'frozen' => 'Beku',
                        'inactive' => 'Nonaktif',
                    ])
                    ->attribute('status'),
            ])
            ->defaultSort('family_no')
            ->striped();
    }

    /**
     * Get list of periods (YYYY-MM) for past 13 months + current.
     */
    private function getPeriods(): array
    {
        $periods = [];
        $now = now();
        // Show 13 months: current month + 12 months back
        for ($i = 12; $i >= 0; $i--) {
            $periods[] = $now->copy()->subMonths($i)->format('Y-m');
        }
        return $periods;
    }

    /**
     * Get dynamic table columns for each period.
     */
    private function getPeriodColumns(array $periods): array
    {
        $columns = [];
        foreach ($periods as $period) {
            $label = \Carbon\Carbon::createFromFormat('Y-m', $period)->format('M\'y');
            $columns[] = TextColumn::make("bill_{$period}")
                ->label($label)
                ->alignCenter()
                ->getStateUsing(fn (FamilyCard $record) => $this->getBillLabel($record, $period))
                ->extraAttributes(fn (FamilyCard $record) => $this->getBillCellAttributes($record, $period))
                ->html();
        }
        return $columns;
    }

    /**
     * Get bill label for a specific KK and period (return HTML string).
     */
    private function getBillLabel(FamilyCard $card, string $period): string
    {
        $bill = $card->monthlyBills->firstWhere('period', $period);
        if (!$bill) {
            return '—';
        }

        $totalPaid = (float) Payment::where('monthly_bill_id', $bill->id)->sum('amount');
        $isPaid = $totalPaid >= (float) $bill->amount;

        if ($isPaid) {
            return '✓ Lunas';
        }

        if ($bill->status === 'overdue' || $bill->status === 'unpaid') {
            $remaining = (float) $bill->amount - $totalPaid;
            return $totalPaid > 0
                ? "Rp " . number_format($remaining, 0, ',', '.')
                : '⬜';
        }

        return '?';
    }

    /**
     * CSS classes for the bill status cell.
     */
    private function getBillCellAttributes(FamilyCard $card, string $period): array
    {
        $bill = $card->monthlyBills->firstWhere('period', $period);
        if (!$bill) {
            return ['class' => 'text-gray-400 text-center px-1'];
        }

        $totalPaid = (float) Payment::where('monthly_bill_id', $bill->id)->sum('amount');
        $isPaid = $totalPaid >= (float) $bill->amount;

        if ($isPaid) {
            $classes = 'bg-green-50 text-green-700 font-medium rounded text-center px-1';
        } elseif ($bill->status === 'overdue') {
            $classes = 'bg-red-50 text-red-700 font-bold rounded text-center px-1';
        } elseif ($bill->status === 'unpaid') {
            $classes = 'bg-yellow-50 text-yellow-700 rounded text-center px-1';
        } else {
            $classes = 'text-gray-400 text-center px-1';
        }

        return ['class' => $classes];
    }

    /**
     * Calculate total outstanding (all unpaid bills for this KK).
     */
    private function calculateOutstanding(FamilyCard $card): float
    {
        $unpaidBills = MonthlyBill::where('family_card_id', $card->id)
            ->whereIn('status', ['unpaid', 'overdue'])
            ->get();

        $totalDue = 0;
        foreach ($unpaidBills as $bill) {
            $paid = (float) Payment::where('monthly_bill_id', $bill->id)->sum('amount');
            $totalDue += max(0, (float) $bill->amount - $paid);
        }

        return $totalDue;
    }
}
