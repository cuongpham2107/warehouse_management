<?php

namespace App\Filament\Resources\ShippingRequests\RelationManagers;

use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\AssociateAction;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('product_name')
                ->label('Tên sản phẩm')
                ->required()
                ->maxLength(255),
            TextInput::make('quantity_requested')
                ->label('Số lượng yêu cầu')
                ->required()
                ->numeric(),
            TextInput::make('quantity_shipped')
                ->label('Số lượng đã giao')
                ->numeric()
                ->default(0),
            TextInput::make('status')
                ->label('Trạng thái')
                ->required()
                ->maxLength(50),
        ]);
    }
    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->heading('📦 Danh sách kiện hàng')
            ->description('')
            ->columns([
                TextColumn::make('crate_id')
                    ->label('Mã kiện hàng')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                TextColumn::make('quantity_requested')
                    ->label('Số lượng yêu cầu'),
                TextColumn::make('quantity_shipped')
                    ->label('Số lượng đã giao'),
                TextColumn::make('status')
                    ->label('Trạng thái'),
            ])
            ->filters([
                // Thêm filter nếu cần
            ])
            ->headerActions([
                

            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }
}
