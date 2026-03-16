<?php

namespace App\Filament\Resources\Currencies\RelationManagers;

use App\Models\Currency;
use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ExchangeRatesBaseRelationManager extends RelationManager
{
    protected static string $relationship = 'exchangeRatesBase';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Base currency')
                    ->disabled()
                    ->default(fn () => $this->ownerRecord->name),
                Select::make('target_currency_id')
                    ->label('Target currency')
                    ->options(function () {
                        $currencies = Currency::pluck('name', 'id');
                        return array_filter($currencies->toArray(), function ($id) {
                            return $id !== $this->ownerRecord->id;
                        }, ARRAY_FILTER_USE_KEY);
                    })
                    ->required(),
                TextInput::make('rate')
                    ->numeric()
                    ->required(),
            ])->columns(3);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('Name')
            ->columns([
                TextColumn::make('TargetCurrency.name')
                    ->label('Target currency')
                    ->searchable(),
                TextColumn::make('rate')
                    ->label('Exchange rate')
                    ->formatStateUsing(fn ($state) => number_format($state, 2, ',', ' ') . ' ' . $this->ownerRecord->symbol)
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Creation date')
                    ->date('d/m/Y')
                    ->sortable()
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
                AssociateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DissociateAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
