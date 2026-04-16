<?php

namespace App\Enums;

use BackedEnum;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

enum TypeAccount: string implements HasLabel, HasColor, HasIcon
{
    case USER = 'user';
    case SYSTEM = 'system';

    public function getLabel(): string|Htmlable|null
    {
        return match ($this) {
            self::USER => 'User',
            self::SYSTEM => 'System',
        };
    }
    
    public function getColor(): string | array | null
    {
        return match ($this) {
            self::USER => 'primary',
            self::SYSTEM => 'secondary',
        };
    }

    public function getIcon(): string | BackedEnum | Htmlable | null
    {
        return match ($this) {
            self::USER => Heroicon::User,
            self::SYSTEM => Heroicon::Cog,
        };
    }
}
