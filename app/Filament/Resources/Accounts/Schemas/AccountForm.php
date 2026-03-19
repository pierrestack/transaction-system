<?php

namespace App\Filament\Resources\Accounts\Schemas;

use App\Enums\StatusAccount;
use App\Models\Currency;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class AccountForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('currency_id')
                    ->label('Currency')
                    ->required()
                    ->selectablePlaceholder(fn (string $operation): ?string => $operation === 'create' ? 'Choose a currency...' : null)
                    ->options(fn () => Currency::pluck('name', 'id')),
                TextInput::make('account_number')
                    ->default(fn () => Str::uuid()->toString())
                    ->disabled()
                    ->dehydrated(true)
                    ->unique(ignoreRecord: true)
                    ->required(),
                TextInput::make('balance')
                    ->required()
                    ->numeric()
                    ->disabled()
                    ->dehydrated(true)
                    ->unique(ignoreRecord: true)
                    ->default(0),
                Select::make('status')
                    ->required()
                    ->selectablePlaceholder(fn (string $operation): ?string => $operation === 'create' ? 'Choose a status...' : null)
                    ->options(StatusAccount::class)
                    ->default('active'),
            ]);
    }
}
