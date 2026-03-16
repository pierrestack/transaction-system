<?php

namespace App\Enums;

use BackedEnum;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

enum StatusAccount: string implements HasLabel, HasColor, HasIcon
{
    case ACTIVE = 'active';
    case SUSPENDED = 'suspended';
    case CLOSED = 'closed';

    public function getLabel(): string|Htmlable|null
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::SUSPENDED => 'Suspended',
            self::CLOSED => 'Closed',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::ACTIVE => 'success',
            self::SUSPENDED => 'warning',
            self::CLOSED => 'danger',
        };
    }

    public function getIcon(): string | BackedEnum | Htmlable | null
    {
        return match ($this) {
            self::ACTIVE    => Heroicon::CheckCircle,
            self::SUSPENDED => Heroicon::ExclamationTriangle,
            self::CLOSED    => Heroicon::XCircle,
        };
    }
}
