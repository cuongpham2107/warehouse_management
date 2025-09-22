<?php

namespace App\Filament\Resources\Pallets\Tables;

use App\Enums\ShippingRequestStatus;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Tables\Grouping\Group;
use Illuminate\Database\Eloquent\Collection;
use App\Filament\Resources\ShippingRequests\Schemas\ShippingRequestForm;
use App\Models\ShippingRequest;
use Filament\Actions\Action;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Textarea;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ShippingInvoiceExportController;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Schemas\Components\Section;

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
                    ->width('15%')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                TextColumn::make('crate.crate_id')
                    ->label('Mã kiện hàng')
                     ->width('15%')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('crate.pcs')
                    ->label('PCS')
                     ->alignCenter()
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('crate.gross_weight')
                    ->label('Trọng lượng')
                     ->alignCenter()
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
                    ->alignCenter()
                    ->badge()
                    ->color(fn($state) => $state instanceof \App\Enums\PalletStatus ? $state->getColor() : 'gray')
                    ->icon(fn($state) => $state instanceof \App\Enums\PalletStatus ? $state->getIcon() : null)
                    ->formatStateUsing(fn($state) => $state instanceof \App\Enums\PalletStatus ? $state->getLabel() : ($state ?? 'N/A'))
                    ->sortable(),

                // TextColumn::make('checked_in_at')
                //     ->label('Thời gian nhập kho')
                //     ->dateTime('H:i d/m/Y')
                //     ->sortable()
                //     ->toggleable(),

                // TextColumn::make('checkedInBy.name')
                //     ->label('Người nhập kho')
                //     ->toggleable(isToggledHiddenByDefault: true),

                // TextColumn::make('checked_out_at')
                //     ->label('Thời gian xuất kho')
                //     ->dateTime('H:i d/m/Y')
                //     ->sortable()
                //     ->toggleable(),

                // TextColumn::make('checkedOutBy.name')
                //     ->label('Người xuất kho')
                //     ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('H:i d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Ngày cập nhật')
                    ->dateTime('H:i d/m/Y')
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
                    ->modifyFormFieldUsing(fn($field) => $field->default('stored'))
                    ->default(),

                SelectFilter::make('receivingPlan')
                    ->label('Thuộc kế hoạch nhập kho')
                    ->relationship('crate.receivingPlan', 'plan_code'),
                Filter::make('list_pallets')
                    ->schema(schema: [
                        Textarea::make('pallet_ids')
                            ->label('Danh sách mã pallet')
                            ->helperText('Nhập mã pallet, mỗi mã trên một dòng')
                            ->placeholder('Nhập mã pallet để lọc')
                            ->rows(5)
                            ->columnSpanFull(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (empty($data['pallet_ids'])) {
                            return $query;
                        }
                        return $query->whereIn('pallet_id', explode("\n", trim($data['pallet_ids'] ?? '')));
                    }),
                Filter::make('list_crates')
                    ->schema(schema: [
                        Textarea::make('crate_ids')
                            ->label('Danh sách mã kiện hàng')
                            ->helperText('Nhập mã kiện hàng, mỗi mã trên một dòng')
                            ->placeholder('Nhập mã kiện hàng để lọc')
                            ->rows(5)
                            ->columnSpanFull(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (empty($data['crate_ids'])) {
                            return $query;
                        }
                        $crateIds = array_map('trim', explode("\n", trim($data['crate_ids'])));
                        return $query->whereHas('crate', function ($q) use ($crateIds) {
                            $q->whereIn('crate_id', $crateIds);
                        });
                    }),


            ])
            ->defaultGroup('crate.receivingPlan.plan_code')
            ->groups([
                Group::make('crate.receivingPlan.plan_code')
                    ->label('Thuộc kế hoạch nhập kho')
                    ->collapsible(),

            ])
            ->recordActions([
                EditAction::make()
                    ->icon('heroicon-o-pencil')
                    ->iconButton(),
                DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->iconButton(),
                ViewAction::make()
                    ->icon('heroicon-o-eye')
                    ->iconButton()
                    ->modalHeading('Thông tin xuất kho của pallet')
                    ->visible(fn ($record) => $record->shippingRequestItem && $record->shippingRequestItem->shippingRequest)
                    ->fillForm(function ($record) {
                        $shippingRequest = $record->shippingRequestItem?->shippingRequest;
                        
                        if (!$shippingRequest) {
                            return [];
                        }
                        
                        return [
                            'lifting_time' => $shippingRequest->lifting_time ? 
                                \Carbon\Carbon::parse($shippingRequest->lifting_time)->format('H:i d/m/Y') : 
                                'Chưa có thông tin',
                            'requested_date' => $shippingRequest->requested_date ? 
                                \Carbon\Carbon::parse($shippingRequest->requested_date)->format('H:i d/m/Y') : 
                                'Chưa có thông tin',
                            'license_plate' => $shippingRequest->license_plate ?? 'Chưa có thông tin',
                            'driver_name' => $shippingRequest->driver_name ?? 'Chưa có thông tin',
                            'driver_phone' => $shippingRequest->driver_phone ?? 'Chưa có thông tin',
                            'seal_number' => $shippingRequest->seal_number ?? 'Chưa có thông tin',
                        ];
                    })
                    ->schema([
                        Section::make('')
                            ->columns(4)
                            ->schema([
                                TextInput::make('lifting_time')
                                    ->label('Thời gian đóng hàng')
                                    ->columnSpan(2)
                                    ->disabled(),
                                TextInput::make('requested_date')
                                    ->label('Ngày giao hàng')
                                    ->columnSpan(2)
                                    ->disabled(),
                                TextInput::make('license_plate')
                                    ->label('Biển số xe')
                                    ->disabled(),
                                TextInput::make('driver_name')
                                    ->label('Tên tài xế')
                                    ->disabled(),
                                TextInput::make('driver_phone')
                                    ->label('SĐT tài xế')
                                    ->disabled(),
                                TextInput::make('seal_number')
                                    ->label('Số niêm phong')
                                    ->disabled(),
                            ])
                    ])
            ],position: RecordActionsPosition::BeforeColumns)
            ->recordUrl(null)
            ->headerActions([

                BulkAction::make('choose_crate_export_warehouse')
                    ->label('Xuất kho')
                    ->icon('heroicon-o-check')
                    ->color('primary')
                    ->modalHeading('Chi tiết yêu cầu xuất kho')
                    ->modalDescription('Vui lòng nhập các thông tin cần thiết để xuất kho các kiện hàng.')
                    ->schema(fn($schema) => ShippingRequestForm::configure($schema))
                    ->modalWidth(\Filament\Support\Enums\Width::SevenExtraLarge)
                    ->visible(
                        fn($records) =>
                        $records && $records->every(
                            fn($record) =>
                            $record && (
                                ($record->status instanceof \App\Enums\PalletStatus && $record->status === \App\Enums\PalletStatus::STORED)
                                || $record->status === \App\Enums\PalletStatus::STORED->value
                            )
                        )
                    )
                    ->fillForm(function (Collection $records) {})
                    ->action(function (Collection $records, array $data, array $arguments) {

                        $records = $records->load('crate');
                        $data['status'] = ShippingRequestStatus::IN_PROGRESS->value;

                        if ($records->isEmpty()) {
                            \Filament\Notifications\Notification::make()
                                ->title('Không có pallet nào được chọn')
                                ->body('Vui lòng chọn ít nhất một pallet để xuất kho.')
                                ->danger()
                                ->send();
                            return;
                        }
                        try {
                            $shippingRequest = ShippingRequest::create($data);
                            if (!$shippingRequest) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Lỗi khi tạo yêu cầu xuất kho')
                                    ->body('Không thể tạo yêu cầu xuất kho. Vui lòng thử lại sau.')
                                    ->danger()
                                    ->send();
                                return;
                            }
                            foreach ($records as $record) {
                                $record->update([
                                    'status' => \App\Enums\PalletStatus::IN_STOCK->value,
                                    'checked_out_at' => now(),
                                    'checked_out_by' => Auth::user()->id,
                                ]);
                                $record->crate->update(['status' => \App\Enums\CrateStatus::PLANNED->value]);
                                $shippingRequest->items()->create([
                                    'crate_id' => $record->crate->id ?? null,
                                    'quantity_shipped' => $record->crate->pieces ?? 0,
                                ]);
                            }
                            if (data_get($arguments, 'export')) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Xuất kho kèm file excel thành công')
                                    ->body("Đã tạo yêu cầu xuất {$shippingRequest->items()->count()} thùng hàng.")
                                    ->success()
                                    ->actions([
                                        Action::make('edit_shipping_request')
                                            ->label('Xem yêu cầu xuất kho')
                                            ->url(route('filament.admin.resources.shipping-requests.edit', $shippingRequest->id))
                                            ->icon('heroicon-o-eye'),
                                    ])
                                    ->send();

                                if ($shippingRequest->items()->count() == 0) {
                                    \Filament\Notifications\Notification::make()
                                        ->title('Không có items để xuất kho')
                                        ->danger()
                                        ->send();
                                    return;
                                }
                                return (new ShippingInvoiceExportController())->export($shippingRequest->id);
                            }
                            \Filament\Notifications\Notification::make()
                                ->title('Xuất kho thành công')
                                ->body("Đã tạo yêu cầu xuất {$shippingRequest->items()->count()} thùng hàng.")
                                ->success()
                                ->actions([
                                    Action::make('edit_shipping_request')
                                        ->label('Xem yêu cầu xuất kho')
                                        ->url(route('filament.admin.resources.shipping-requests.edit', $shippingRequest->id))
                                        ->icon('heroicon-o-eye'),
                                ])
                                ->send();
                        } catch (\Exception $e) {
                            \Filament\Notifications\Notification::make()
                                ->title('Lỗi khi xuất kho')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                            return;
                        }
                    })
                    ->modalSubmitAction(fn(Action $action) => $action->label('Tạo yêu cầu xuất kho'))
                    ->extraModalFooterActions(fn(Action $action): array => [
                        $action->makeModalSubmitAction('createAndExport', arguments: ['export' => true])->label('Tạo và xuất file Excel')->color('success')

                    ]),
                BulkAction::make('export-excel')
                    ->label('Xuất file Excel')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('success')
                    ->action(function (Collection $records) {
                        $records = $records->load([
                            'crate.receivingPlan',
                            'shippingRequestItem.shippingRequest',
                            'checkedInBy',
                            'checkedOutBy'
                        ]);
                        $fileName = 'pallets_export_' . now()->format('Y_m_d_H_i_s') . '.xlsx';
                        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\PalletExport($records), $fileName);
                    })
                    ->requiresConfirmation(),
                       
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Xóa đã chọn'),
                ])->label('Hành động hàng loạt'),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50, 100, 'all'])
            ->reorderableColumns();
    }
}
