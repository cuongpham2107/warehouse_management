<?php

namespace App\Filament\Resources\ShippingRequests\RelationManagers;

use App\Filament\Resources\Shipments\Schemas\ShippingRequestItemForm;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;

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
                TextColumn::make('quantity_requested')
                    ->label('Số kiện yêu cầu')
                    ->color('primary')
                    ->alignCenter(),
                TextColumn::make('quantity_shipped')
                    ->label('Số kiện đã xuất kho')
                    ->color('success')
                    ->alignCenter(),
                TextColumn::make('status')
                    ->label('Trạng thái')
                    ->color(fn($state): string => $state instanceof \App\Enums\ShippingRequestItemStatus ? ($state->getColor() ?? 'gray') : 'gray')
                    ->icon(fn($state): string => $state instanceof \App\Enums\ShippingRequestItemStatus ? ($state->getIcon() ?? 'heroicon-m-cube') : 'heroicon-m-cube')
                    ->formatStateUsing(fn($state): string => $state instanceof \App\Enums\ShippingRequestItemStatus ? $state->getLabel() : ($state ?? 'N/A'))
                    ->badge(),
            ])
            ->reorderableColumns()
            ->filters([
                SelectFilter::make('status')
                    ->options(\App\Enums\ShippingRequestItemStatus::getOptions())
                    ->label('Trạng thái'),
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
                EditAction::make()
                    ->label('Chỉnh sửa')
                    ->modalHeading('Chỉnh sửa kiện hàng')
                    ->modalSubmitActionLabel('Cập nhật kiện hàng')
                    ->successNotificationTitle('Kiện hàng đã được cập nhật thành công')
                    ->schema(fn (Schema $schema) => ShippingRequestItemForm::configure($schema)),
                DeleteAction::make()
                    ->label('Xóa')
                    ->modalHeading('Xác nhận xóa kiện hàng')
                    ->modalSubmitActionLabel('Xóa kiện hàng')
                    ->successNotificationTitle('Kiện hàng đã được xóa thành công')
                    ->requiresConfirmation()
                    ->color('danger'),  
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }
}
