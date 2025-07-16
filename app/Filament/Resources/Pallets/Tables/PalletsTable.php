<?php

namespace App\Filament\Resources\Pallets\Tables;

use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Tables\Grouping\Group;
use Filament\Forms\Components\Select;
use Filament\Support\Enums\Width;
use Illuminate\Database\Eloquent\Collection;
use App\Filament\Resources\ShippingRequests\Schemas\ShippingRequestForm;
use App\Models\ShippingRequest;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PalletExport;

class PalletsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('pallet_id')
                    ->label('Mã pallet')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                    
                TextColumn::make('crate.crate_id')
                    ->label('Thùng hàng')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('crate.receivingPlan.plan_code')
                    ->label('Kế hoạch nhập kho')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                    
                TextColumn::make('location.location_code')
                    ->label('Vị trí')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                    
                TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->color(fn ($state) => $state instanceof \App\Enums\PalletStatus ? $state->getColor() : 'gray')
                    ->icon(fn ($state) => $state instanceof \App\Enums\PalletStatus ? $state->getIcon() : null)
                    ->formatStateUsing(fn ($state) => $state instanceof \App\Enums\PalletStatus ? $state->getLabel() : ($state ?? 'N/A'))
                    ->sortable(),
                    
                TextColumn::make('checked_in_at')
                    ->label('Thời gian nhập kho')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),
                    
                TextColumn::make('checkedInBy.name')
                    ->label('Người nhập kho')
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                TextColumn::make('checked_out_at')
                    ->label('Thời gian xuất kho')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),
                    
                TextColumn::make('checkedOutBy.name')
                    ->label('Người xuất kho')
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                TextColumn::make('updated_at')
                    ->label('Ngày cập nhật')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options([
                        'in_transit' => 'Đang vận chuyển',
                        'received' => 'Đã nhận',
                        'stored' => 'Đã lưu kho',
                        'shipped' => 'Đã xuất kho',
                        'damaged' => 'Bị hư hỏng',
                    ])
                    ->native(false),
                    
                SelectFilter::make('location_id')
                    ->label('Vị trí')
                    ->relationship('location', 'location_code')
                    ->searchable()
                    ->preload()
                    ->native(false),
            ])
            ->defaultGroup('crate.receivingPlan.plan_code')
            ->groups([
                Group::make('crate.receivingPlan.plan_code')
                    ->label('Thuộc kế hoạch nhập kho')
                    ->collapsible(),
            ])
            ->recordActions([
                ViewAction::make()->label('Xem'),
                EditAction::make()->label('Sửa'),
            ])
            ->headerActions([
                BulkAction::make('choose_crate_export_warehouse')
                    ->label('Chọn thùng hàng để xuất kho')
                    ->icon('heroicon-o-check')
                    ->color('primary')
                    ->modalHeading('Chi tiết yêu cầu xuất kho')
                    ->modalDescription('Vui lòng nhập các thông tin cần thiết để xuất kho các kiện hàng.')
                    ->schema(fn ($schema) => ShippingRequestForm::configure($schema))
                    ->fillForm(function (Collection $records) {})
                    ->action(function (Collection $records, array $data) {
                        
                        $records = $records->load('crate')->map(function ($record) {
                            return [
                                'id' => $record->crate->id ?? null,
                                'pieces' => $record->crate->pieces ?? 0,
                            ];
                        })->toArray();
                        
                        if (empty($records)) {
                            \Filament\Notifications\Notification::make()
                                ->title('Không có pallet nào được chọn')
                                ->body('Vui lòng chọn ít nhất một pallet để xuất kho.')
                                ->danger()
                                ->send();
                            return;
                        }
                        try {
                            $shippingRequest = ShippingRequest::create($data);
                            if(!$shippingRequest) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Lỗi khi tạo yêu cầu xuất kho')
                                    ->body('Không thể tạo yêu cầu xuất kho. Vui lòng thử lại sau.')
                                    ->danger()
                                    ->send();
                                return;
                            }
                            foreach ($records as $record) {
                                $shippingRequest->items()->create([
                                    'crate_id' => $record['id'],
                                    'quantity_requested' => $record['pieces'],
                                    'status' => 'pending', // Đã tạo yêu cầu xuất kho, chưa xử lý
                                ]);
                            }
                            \Filament\Notifications\Notification::make()
                            ->title('Xuất kho thành công')
                            ->body("Đã tạo yêu cầu xuất {$shippingRequest->items()->count()} thùng hàng.")
                            ->success()
                            ->send();
                        }
                        catch (\Exception $e) {
                            \Filament\Notifications\Notification::make()
                                ->title('Lỗi khi xuất kho')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                            return;
                        }
                       
                    }),
                BulkAction::make('export_excel')
                    ->label('Xuất Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function (Collection $records) {
                        return Excel::download(new PalletExport($records), 'pallets_export.xlsx');
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Xóa đã chọn'),
                ])->label('Hành động hàng loạt'),
            ])
            ->checkIfRecordIsSelectableUsing(fn($record) =>
                ($record->status instanceof \App\Enums\PalletStatus && $record->status === \App\Enums\PalletStatus::STORED)
                || $record->status === \App\Enums\PalletStatus::STORED->value
            )
            ->defaultSort('created_at', 'desc')
            ->reorderableColumns();
    }
}
