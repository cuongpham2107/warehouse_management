<?php

namespace App\Filament\Resources\Shipments\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use App\Filament\Resources\Shipments\Schemas\ShipmentItemForm;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ShipmentItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'shipmentItems';

    public function form(Schema $schema): Schema
    {
        return ShipmentItemForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return $table
            // ->recordTitleAttribute('pallet_id')
            ->columns([
                TextColumn::make('pallet.pallet_id')
                    ->searchable(),
                TextColumn::make('crate.crate_id')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Trạng thái')
                    ->formatStateUsing(fn($state) => $state instanceof \App\Enums\ShipmentItemStatus ? $state->getLabel() : ($state ? \App\Enums\ShipmentItemStatus::from($state)->getLabel() : ''))
                    ->icon(fn ($state) => $state instanceof \App\Enums\ShipmentItemStatus ? $state->getIcon() : ($state ? \App\Enums\ShipmentItemStatus::from($state)->getIcon() : ''))
                    ->searchable()
                    ->badge()
                    ->color(fn ($state) => $state instanceof \App\Enums\ShipmentItemStatus ? $state->getColor() : ($state ? \App\Enums\ShipmentItemStatus::from($state)->getColor() : '')), 

                TextColumn::make('quantity')
                    ->searchable(),
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
            ])
            ->reorderableColumns();
    }
}
