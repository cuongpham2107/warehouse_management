<?php

namespace App\Filament\Resources\Shipments\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
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
                TextColumn::make('quantity')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // CreateAction::make(),
                // AssociateAction::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                // DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->reorderableColumns();
    }
}
