<?php

namespace App\Filament\Widgets;

use App\Models\FamilyCard;
use App\Models\Member;
use App\Models\MonthlyBill;
use App\Models\Region;
use App\Models\SupportBody;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalFamilyCards = FamilyCard::count();
        $activeFamilyCards = FamilyCard::where('status', 'active')->count();
        $frozenFamilyCards = FamilyCard::where('status', 'frozen')->count();

        $totalMembers = Member::count();
        $activeMembers = Member::where('status', 'active')->count();
        $deceasedMembers = Member::where('status', 'deceased')->count();

        $totalRegions = Region::count();
        $totalBodies = SupportBody::count();

        $totalBills = MonthlyBill::where('status', 'unpaid')->sum('amount') +
            MonthlyBill::where('status', 'overdue')->sum('amount');
        $totalOverdue = MonthlyBill::where('status', 'overdue')->sum('amount');

        $complianceRate = 0;
        $totalBillCount = MonthlyBill::count();
        if ($totalBillCount > 0) {
            $paidCount = MonthlyBill::where('status', 'paid')->count();
            $complianceRate = round(($paidCount / $totalBillCount) * 100);
        }

        return [
            Stat::make('Total KK', number_format($totalFamilyCards))
                ->description("{$activeFamilyCards} aktif, {$frozenFamilyCards} beku")
                ->descriptionIcon('heroicon-o-identification')
                ->color('success'),

            Stat::make('Total Anggota', number_format($totalMembers))
                ->description("{$activeMembers} aktif, {$deceasedMembers} meninggal")
                ->descriptionIcon('heroicon-o-user-group')
                ->color('info'),

            Stat::make('Wilayah & Badan Pembantu', "{$totalRegions} / {$totalBodies}")
                ->description('Wilayah / Badan Pembantu')
                ->descriptionIcon('heroicon-o-map-pin')
                ->color('warning'),

            Stat::make('Total Tunggakan', 'Rp ' . number_format($totalBills, 0, ',', '.'))
                ->description("Terlambat: Rp " . number_format($totalOverdue, 0, ',', '.'))
                ->descriptionIcon('heroicon-o-document-text')
                ->color('danger'),

            Stat::make('Kepatuhan Iuran', "{$complianceRate}%")
                ->description('Persentase pembayaran tepat waktu')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),
        ];
    }
}
