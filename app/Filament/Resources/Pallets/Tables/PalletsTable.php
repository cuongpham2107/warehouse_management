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
use Illuminate\Database\Eloquent\Collection;
use App\Filament\Resources\ShippingRequests\Schemas\ShippingRequestForm;
use App\Models\ShippingRequest;
use Filament\Actions\Action;
use App\Exports\ShippingRequestInvoiceExport;
use Maatwebsite\Excel\Facades\Excel;


class PalletsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            // ->heading('Danh sách pallet')
            ->description('Trạng thái của các pallet phải là "Đã lưu kho" để có thể xuất kho.')
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
                    
                TextColumn::make('location_code')
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
                SelectFilter::make('receivingPlan')
                    ->label('Thuộc kế hoạch nhập kho')
                    ->relationship('crate.receivingPlan', 'id')
                    
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
                    ->label('Xuất kho')
                    ->icon('heroicon-o-check')
                    ->color('primary')
                    ->modalHeading('Chi tiết yêu cầu xuất kho')
                    ->modalDescription('Vui lòng nhập các thông tin cần thiết để xuất kho các kiện hàng.')
                    ->schema(fn ($schema) => ShippingRequestForm::configure($schema))
                    ->visible(fn($records) =>
                        $records && $records->every(fn ($record) => 
                            $record && (
                                ($record->status instanceof \App\Enums\PalletStatus && $record->status === \App\Enums\PalletStatus::STORED)
                                || $record->status === \App\Enums\PalletStatus::STORED->value
                            )
                        )
                    )
                    ->fillForm(function (Collection $records) {})
                    ->action(function (Collection $records, array $data, array $arguments) {
                        
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
                                    'quantity_shipped' => $record['pieces'],
                                    'status' => 'pending', // Đã tạo yêu cầu xuất kho, chưa xử lý
                                ]);
                            }
                            if (data_get($arguments, 'export')) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Xuất kho kèm file excel thành công')
                                    ->body("Đã tạo yêu cầu xuất {$shippingRequest->items()->count()} thùng hàng.")
                                    ->success()
                                    ->actions([
                                        \Filament\Actions\Action::make('edit_shipping_request')
                                            ->label('Xem yêu cầu xuất kho')
                                            ->url(route('filament..resources.shipping-requests.edit', $shippingRequest->id))
                                            ->icon('heroicon-o-eye'),
                                    ])
                                    ->send();
                                return Excel::download(new ShippingRequestInvoiceExport($shippingRequest), 'shipping_request_invoice.xlsx');
                            }
                            \Filament\Notifications\Notification::make()
                            ->title('Xuất kho thành công')
                            ->body("Đã tạo yêu cầu xuất {$shippingRequest->items()->count()} thùng hàng.")
                            ->success()
                            ->actions([
                                \Filament\Actions\Action::make('edit_shipping_request')
                                    ->label('Xem yêu cầu xuất kho')
                                    ->url(route('filament..resources.shipping-requests.edit', $shippingRequest->id))
                                    ->icon('heroicon-o-eye'),
                            ])
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
                       
                    })
                    ->modalSubmitAction(fn(Action $action) => $action->label('Tạo yêu cầu xuất kho'))
                    ->extraModalFooterActions(fn (Action $action): array => [
                       $action->makeModalSubmitAction('createAndExport', arguments: ['export' => true])->label('Tạo và xuất file Excel')->color('success')
                        
                    ])

            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Xóa đã chọn'),
                ])->label('Hành động hàng loạt'),
            ])
            ->defaultSort('created_at', 'desc')
            ->reorderableColumns();
    }
}
