<?php

namespace App\Filament\Widgets;

use App\Models\Account;
use App\Models\Operation;
use App\Models\Transfer;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total accounts', Account::count())
                ->description('Total number of accounts')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Total transfers', Transfer::count())
                ->description('Total number of transfers')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('warning'),

            Stat::make('Total operations', Operation::count())
                ->description('Total number of operations')
                ->descriptionIcon('heroicon-m-arrows-right-left')
                ->color('info'),
        ];
    }
}
