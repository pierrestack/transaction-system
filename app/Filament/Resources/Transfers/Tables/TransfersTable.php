<?php

namespace App\Filament\Resources\Transfers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class TransfersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reference')
                    ->label('Reference')
                    ->searchable(),
                TextColumn::make('senderAccount.account_number')
                    ->label('Sender')
                    ->formatStateUsing(fn (string $state) => $state ? $state : '--')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('receiverAccount.account_number')
                    ->label('Receiver')
                    ->formatStateUsing(fn (string $state) => $state ? $state : '--')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('amount')
                    ->numeric()
                    ->formatStateUsing(fn (string $state, Model $record) => number_format($state, 2, ',', ' '). ' '. $record->currency->symbol)
                    ->sortable(),
                TextColumn::make('type')
                    ->badge()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('fee.amount')
                    ->label('fee')
                    ->numeric()
                    ->formatStateUsing(fn (string $state, Model $record) => number_format($state, 2, ',', ' '). ' '. $record->currency->symbol)
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                // EditAction::make(),
            ])
            ->toolbarActions([
//                BulkActionGroup::make([
//                    DeleteBulkAction::make(),
//                ]),
            ]);
    }
}
