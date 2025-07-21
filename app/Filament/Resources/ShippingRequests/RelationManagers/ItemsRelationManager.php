<?php

namespace App\Filament\Resources\ShippingRequests\RelationManagers;

use App\Filament\Resources\Shipments\Schemas\ShippingRequestItemForm;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\CreateAction;
use Filament\Actions\ViewAction;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
        ]);
    }
    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->heading('📦 Danh sách kiện hàng')
            ->description('')
            ->columns([
                TextColumn::make('crate.crate_id')
                    ->label('Mã kiện hàng')
                    ->searchable(),
                TextColumn::make('crate.pieces')
                    ->label('Số kiện')
                    ->color('info')
                    ->badge()
                    ->alignCenter(),
                TextColumn::make('crate.gross_weight')
                    ->label('Tổng trọng lượng kiện hàng (kg)')
                    ->color('warning')
                    ->badge()
                    ->alignCenter(),
                TextColumn::make('quantity_shipped')
                    ->label('Số kiện đã xuất kho')
                    ->color('success')
                    ->alignCenter(),
                
            ])
            ->reorderableColumns()
            ->filters([
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Thêm kiện hàng')
                    ->modalHeading('Thêm kiện hàng mới')
                    ->modalSubmitActionLabel('Tạo kiện hàng')
                    ->successNotificationTitle('Kiện hàng đã được tạo thành công')
                    ->schema(fn (Schema $schema) => ShippingRequestItemForm::configure($schema)),
        
            ])
             ->recordActions([
                ViewAction::make()
                    ->label('Xem')
                    ->modalHeading('Xem kiện hàng')
                    ->modalSubmitActionLabel('Xem kiện hàng')
                    ->successNotificationTitle('Kiện hàng đã được xem thành công')
                    ->schema(fn (Schema $schema) => ShippingRequestItemForm::configure($schema)),
                
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }
}
