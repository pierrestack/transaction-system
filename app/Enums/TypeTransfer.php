<?php

namespace App\Enums;

use BackedEnum;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

enum TypeTransfer: string implements HasLabel, HasColor, HasIcon
{
    case DEPOSIT = 'deposit';
    case WITHDRAW = 'withdraw';

    public function getLabel(): string|Htmlable|null
    {
        return match ($this) {
            self::DEPOSIT => 'Deposit',
            self::WITHDRAW => 'Withdraw',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::DEPOSIT => 'success',
            self::WITHDRAW => 'danger',
        };
    }

    public function getIcon(): string | BackedEnum | Htmlable | null
    {
        return match ($this) {
            self::DEPOSIT => Heroicon::ArrowUpCircle,
            self::WITHDRAW => Heroicon::ArrowDownCircle,
        };
    }
}
