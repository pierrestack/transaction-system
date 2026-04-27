<?php

namespace App\Filament\Resources\LedgerEntries\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class LedgerEntriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) =>
                $query->orderBy('created_at', 'desc')
                )
            ->columns([
                TextColumn::make('fromCurrency.code')
                    ->label('Source'),
                TextColumn::make('toCurrency.code')
                    ->label('Target'),
                TextColumn::make('exchange_rate')
                    ->numeric()
                    ->formatStateUsing(function (string $state, Model $record) {
                        if($record->fromCurrency->code === 'MGA'){
                            return number_format($state, 6, ',', ' '). ' '. $record->toCurrency->symbol;
                        } else {
                            return number_format($state, 2, ',', ' '). ' '. $record->toCurrency->symbol;
                        }
                    }),
                TextColumn::make('source_amount')
                    ->numeric()
                    ->formatStateUsing(fn (string $state, Model $record) => number_format($state, 2, ',', ' '). ' '. $record->fromCurrency->symbol),
                TextColumn::make('target_amount')
                    ->numeric()
                    ->formatStateUsing(fn (string $state, Model $record) => number_format($state, 2, ',', ' '). ' '. $record->toCurrency->symbol),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
//                EditAction::make(),
            ])
            ->toolbarActions([
//                BulkActionGroup::make([
//                    DeleteBulkAction::make(),
//                ]),
            ]);
    }
}
