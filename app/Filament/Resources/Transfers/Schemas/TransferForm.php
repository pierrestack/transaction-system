<?php

namespace App\Filament\Resources\Transfers\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class TransferForm
{
    public ?array $depositData = [];
    public ?array $withdrawData = [];
    public ?array $transferData = [];

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Transactions')
                    ->tabs([
                        Tab::make('Deposit')
                            ->schema(self::getFields('deposit'))
                            ->statePath('depositData'),
                        Tab::make('Withdrawal')
                            ->schema(self::getFields('withdrawal'))
                            ->statePath('withdrawData'),
                        Tab::make('Transfer')
                            ->schema(self::getFields('transfer'))
                            ->statePath('transferData'),
                    ])->columnSpanFull(),
            ]);
    }

    private static function getFields(string $type): array
    {
        $fields = [];

        if ($type === 'deposit') {
            $fields[] = Select::make('receiver_account_id')
                ->label('Account')
                ->relationship('receiverAccount', 'account_number')
                ->required();
        }

        if ($type === 'withdrawal') {
            $fields[] = Select::make('sender_account_id')
                ->label('Account')
                ->relationship('senderAccount', 'account_number')
                ->required();
        }

        if ($type === 'transfer') {
            $fields[] = Select::make('sender_account_id')
                ->label('Sender')
                ->relationship('senderAccount', 'account_number')
                ->required();

            $fields[] = Select::make('receiver_account_id')
                ->label('Receiver')
                ->relationship('receiverAccount', 'account_number')
                ->required();
        }

        $fields[] = TextInput::make('amount')
            ->label('Amount')
            ->numeric()
            ->required();

        $fields[] = Textarea::make('description')
            ->label('Description')
            ->rows(3)
            ->maxLength(255);

        return $fields;
    }
}
