<?php

namespace App\Filament\Resources\Accounts\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class OperationsRelationManager extends RelationManager
{
    protected static string $relationship = 'operations';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('account_number')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('account_number')
            ->modifyQueryUsing(fn (Builder $query) =>
                $query->orderBy('created_at', 'desc')
            )
            ->columns([
                TextColumn::make('type')
                    ->searchable()
                    ->formatStateUsing(fn (string $state) => ucfirst($state)),
                TextColumn::make('amount')
                    ->numeric()
                    ->formatStateUsing(fn (string $state, Model $record) => number_format($state, 2, ',', ' ') . ' ' . $record->account->currency->symbol),
                TextColumn::make('balance_before')
                    ->numeric()
                    ->formatStateUsing(fn (string $state, Model $record) => number_format($state, 2, ',', ' ') . ' ' . $record->account->currency->symbol),
                TextColumn::make('balance_after')
                    ->numeric()
                    ->formatStateUsing(fn (string $state, Model $record) => number_format($state, 2, ',', ' ') . ' ' . $record->account->currency->symbol),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                //
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
