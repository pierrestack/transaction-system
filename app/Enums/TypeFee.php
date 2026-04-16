<?php

namespace App\Enums;

use BackedEnum;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

enum TypeFee: string implements HasLabel, HasColor, HasIcon
{
    case FEE_CHARGED = 'fee charged';
    case FREE_CHARGED = 'free charged';

    public function getLabel(): string|Htmlable|null
    {
        return match ($this) {
            self::FEE_CHARGED => 'Fee Charged',
            self::FREE_CHARGED => 'Free Charged',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::FEE_CHARGED => 'danger',
            self::FREE_CHARGED => 'success',
        };
    }

    public function getIcon(): string | BackedEnum | Htmlable | null
    {
        return match ($this) {
            self::FEE_CHARGED => Heroicon::XCircle,
            self::FREE_CHARGED => Heroicon::CheckCircle,
        };
    }
}
