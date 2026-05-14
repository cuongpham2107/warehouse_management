<?php

namespace App\Filament\Resources\ShippingRequests\RelationManagers;

use App\Enums\PalletStatus;
use App\Filament\Resources\ShippingRequestItems\Schemas\ShippingRequestItemForm;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Collection;

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
                TextColumn::make('pallet.pallet_id')
                    ->label('Mã pallet')
                    ->searchable(),
                TextColumn::make('crate.pcs')
                    ->width('10%')
                    ->label('PCS')
                    ->color('info')
                    ->badge()
                    ->alignCenter(),
                TextColumn::make('crate.pieces')
                    ->width('10%')
                    ->label('Quantity')
                    ->color('success')
                    ->alignCenter(),
                TextColumn::make('crate.gross_weight')
                    ->width('15%')
                    ->label('Tổng trọng lượng(kg)')
                    ->color('warning')
                    ->badge()
                    ->alignCenter(),
                TextColumn::make('pallet.status')
                    ->width('10%')
                    ->label('Trạng thái')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state->getLabel())
                    ->color(fn ($state) => $state->getColor()),
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

                // ViewAction::make()
                //     ->label('Xem')
                //     ->modalHeading('Xem kiện hàng')
                //     ->modalSubmitActionLabel('Xem kiện hàng')
                //     ->successNotificationTitle('Kiện hàng đã được xem thành công')
                //     ->schema(fn (Schema $schema) => ShippingRequestItemForm::configure($schema)),

            ])
            ->selectable()
            ->bulkActions([
                // BulkActionGroup::make([
                BulkAction::make('mark_pallet_as_shipped')
                    ->label('Đã xuất kho')
                    ->icon('heroicon-o-check-badge')
                    ->color('primary')
                    ->modalHeading('Xác nhận xuất kho')
                    ->requiresConfirmation()
                    ->modalDescription('Bạn có chắc chắn muốn cập nhật trạng thái của pallet liên quan đến các kiện hàng đã chọn thành "Đã xuất kho"?')
                    ->modalSubmitActionLabel('Cập nhật trạng thái')
                    ->successNotificationTitle('Trạng thái pallet đã được cập nhật thành công')
                    ->action(function (Collection $records): void {
                        if ($records->isEmpty()) {
                            Notification::make()
                                ->title('Chưa có kiện hàng nào được chọn')
                                ->danger()
                                ->send();

                            return;
                        }

                        $allInStock = $records->every(function ($record): bool {
                            $pallet = $record->pallet;

                            return $pallet && (
                                ($pallet->status instanceof PalletStatus && $pallet->status === PalletStatus::IN_STOCK)
                                || $pallet->status === PalletStatus::IN_STOCK->value
                            );
                        });

                        if (! $allInStock) {
                            Notification::make()
                                ->title('Chỉ được xuất kho khi tất cả pallet đang ở trạng thái in_stock')
                                ->danger()
                                ->send();

                            return;
                        }

                        foreach ($records as $record) {
                            $record->pallet?->update(['status' => PalletStatus::SHIPPED->value]);
                        }
                    }),
                // ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }
}
