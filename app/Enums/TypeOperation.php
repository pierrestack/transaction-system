<?php

namespace App\Enums;

use BackedEnum;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

enum TypeOperation: string implements HasLabel, HasColor, HasIcon
{
    case DEBIT = 'debit';
    case CREDIT = 'credit';

    public function getLabel(): string|Htmlable|null
    {
        return match ($this) {
            self::DEBIT => 'Debit',
            self::CREDIT => 'Credit',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::DEBIT => 'danger',
            self::CREDIT => 'success',
        };
    }

    public function getIcon(): string | BackedEnum | Htmlable | null
    {
        return match ($this) {
            self::DEBIT => Heroicon::ArrowDownCircle,
            self::CREDIT => Heroicon::ArrowUpCircle,
        };
    }
}
