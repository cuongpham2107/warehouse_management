<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use App\Models\PalletWithInfo;
use Filament\Tables\Columns\ColumnGroup;
use Illuminate\Database\Eloquent\Builder;
use BackedEnum;
use Filament\Tables\Grouping\Group;

class ReportWarehouse extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'filament.pages.report-warehouse';

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-printer';

    protected static ?int $navigationSort = 4;

    protected ?string $heading = 'Báo cáo tổng hợp';

    public static function getNavigationLabel(): string
    {
        return '3. Báo cáo tổng hợp';
    }

    public static function getModelLabel(): string
    {
        return '3. Báo cáo tổng hợp';
    }

    public static function getPluralModelLabel(): string
    {
        return '3. Báo cáo tổng hợp';
    }

     public function table(Table $table): Table
    {
        return $table
            ->query(PalletWithInfo::query())
            ->columns([
                TextColumn::make('pallet_id')
                    ->label('STT')
                    ->searchable()
                    ->alignCenter()
                    ->toggleable(),
                TextColumn::make('plan_code')
                    ->label('Mã kế hoạch nhận hàng')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('receivingPlan.vendor.vendor_name')
                    ->label('Nhà cung cấp')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                ColumnGroup::make('Hàng hoá')
                    ->columns([
                        TextColumn::make('crate.crate_id')
                            ->label('Mã kiện hàng')
                            ->sortable()
                            ->searchable()
                            ->toggleable(),
                        TextColumn::make('crate_description')
                            ->label('Tên hàng')
                            ->sortable()
                            ->searchable()
                            ->toggleable(),
                        TextColumn::make('crate_pcs')
                            ->label('Số kiện(PCS)')
                            ->alignCenter()
                            ->sortable()
                            ->searchable()
                            ->toggleable(),
                        TextColumn::make('crate_gross_weight')
                            ->label('Trọng lượng (KG)')
                            ->sortable()
                            ->alignCenter()
                            ->searchable()
                            ->toggleable(),
                        TextColumn::make('crate_dimensions')
                            ->label('Kích thước (D x R x C)')
                            ->sortable()
                            ->searchable()
                            ->toggleable(),
                    ]),
                ColumnGroup::make('Nhập kho ASGL')
                    ->columns([
                        TextColumn::make('plan_date_date')
                            ->label('Ngày')
                            ->sortable()
                            ->searchable()
                            ->toggleable(),
                        TextColumn::make('plan_date_date_time')
                            ->label('Giờ')
                            ->sortable()
                            ->searchable()
                            ->toggleable(),
                        TextColumn::make('receiving_license_plate')
                            ->label('Biển số xe')
                            ->sortable()
                            ->searchable()
                            ->toggleable(),
                        TextColumn::make('receiving_transport_garage')
                            ->label('Nhà xe vận chuyển')
                            ->sortable()
                            ->searchable()
                            ->toggleable(),
                        TextColumn::make('receiving_vehicle_capacity')
                            ->label('Tải trọng xe (tấn)')
                            ->sortable()
                            ->alignCenter()
                            ->searchable()
                            ->toggleable(),
                        TextColumn::make('plan_date')
                            ->label('Ngày hàng đến')
                            ->date('d/m/Y')
                            ->alignCenter()
                            ->sortable()
                            ->searchable()
                            ->toggleable(),
                        TextColumn::make('arrival_date')
                            ->label('Hạ hàng')
                            ->date('d/m/Y')
                            ->alignCenter()
                            ->sortable()
                            ->searchable()
                            ->toggleable(),
                        TextColumn::make('checkInBy.name')
                            ->label('Người nhập kho')
                            ->sortable()
                            ->searchable()
                            ->toggleable(),
                    ]),
                    TextColumn::make('receiving_notes')
                        ->label('Ghi chú nhập kho')
                        ->searchable()
                        ->toggleable(),
                    ColumnGroup::make('Xuất kho ASGL')
                        ->columns([
                            TextColumn::make('departure_time_date')
                                ->label('Ngày')
                                ->sortable()
                                ->searchable()
                                ->toggleable(),
                             TextColumn::make('departure_time_time')
                                ->label('Giờ')
                                ->sortable()
                                ->searchable()
                                ->toggleable(),
                            TextColumn::make('shipping_license_plate')
                                ->label('Biển số xe')
                                ->sortable()
                                ->searchable()
                                ->toggleable(),
                            TextColumn::make('shipping_transport_garage')
                                ->label('Nhà xe vận chuyển')
                                ->sortable()
                                ->searchable()
                                ->toggleable(),
                            TextColumn::make('shipping_vehicle_capacity')
                                ->label('Tải trọng xe (tấn)')
                                ->alignCenter()
                                ->sortable()
                                ->searchable()
                                ->toggleable(),
                            TextColumn::make('lifting_time')
                                ->label('Nâng hàng')
                                ->date('d/m/Y')
                                ->alignCenter()
                                ->sortable()
                                ->searchable()
                                ->toggleable(),
                            TextColumn::make('requested_date')
                                ->label('Ngày giao hàng')
                                ->date('d/m/Y')
                                ->alignCenter()
                                ->sortable()
                                ->searchable()
                                ->toggleable(),
                            TextColumn::make('pallet_created_at')
                                ->label('Tạo lúc')
                                ->dateTime('H:i d/m/Y')
                                ->sortable()
                                ->searchable()
                                ->toggleable(),
                            TextColumn::make('customer_name')
                                ->label('Khách hàng')
                                ->sortable()
                                ->searchable()
                                ->toggleable(),
                        ]),
                        TextColumn::make('shipping_notes')
                            ->label('Ghi chú xuất kho')
                            ->searchable()
                            ->toggleable(),
                    


                    ])
            ->striped()
            ->filters([
                // Filter theo trạng thái pallet
                SelectFilter::make('pallet_status')
                    ->label('Trạng thái Pallet')
                    ->options([
                        'in_transit' => 'Đang gắn vị trí và vận chuyển',
                        'stored' => 'Đã lưu kho', 
                        'in_stock' => 'Đang xuất kho',
                        'shipped' => 'Đã xuất kho',
                        'damaged' => 'Bị hư hỏng',
                    ])
                    ->placeholder('Tất cả trạng thái'),

                // Filter theo mã kế hoạch
                SelectFilter::make('plan_code')
                    ->label('Mã kế hoạch')
                    ->options(function () {
                        return PalletWithInfo::query()
                            ->whereNotNull('plan_code')
                            ->distinct()
                            ->pluck('plan_code', 'plan_code')
                            ->toArray();
                    })
                    ->searchable()
                    ->placeholder('Chọn mã kế hoạch'),

                // Filter theo khách hàng
                SelectFilter::make('customer_name')
                    ->label('Khách hàng')
                    ->options(function () {
                        return PalletWithInfo::query()
                            ->whereNotNull('customer_name')
                            ->distinct()
                            ->pluck('customer_name', 'customer_name')
                            ->toArray();
                    })
                    ->searchable()
                    ->placeholder('Chọn khách hàng'),

                // Filter theo nhà xe vận chuyển (nhập kho)
                SelectFilter::make('receiving_transport_garage')
                    ->label('Nhà xe nhập kho')
                    ->options(function () {
                        return PalletWithInfo::query()
                            ->whereNotNull('receiving_transport_garage')
                            ->distinct()
                            ->pluck('receiving_transport_garage', 'receiving_transport_garage')
                            ->toArray();
                    })
                    ->searchable()
                    ->placeholder('Chọn nhà xe nhập kho'),

                // Filter theo nhà xe vận chuyển (xuất kho)
                SelectFilter::make('shipping_transport_garage')
                    ->label('Nhà xe xuất kho')
                    ->options(function () {
                        return PalletWithInfo::query()
                            ->whereNotNull('shipping_transport_garage')
                            ->distinct()
                            ->pluck('shipping_transport_garage', 'shipping_transport_garage')
                            ->toArray();
                    })
                    ->searchable()
                    ->placeholder('Chọn nhà xe xuất kho'),

                // Filter theo ngày hàng đến
                Filter::make('plan_date_range')
                    ->form([
                        DatePicker::make('plan_date_from')
                            ->label('Từ ngày hàng đến'),
                        DatePicker::make('plan_date_to')
                            ->label('Đến ngày hàng đến'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['plan_date_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('plan_date', '>=', $date),
                            )
                            ->when(
                                $data['plan_date_to'],
                                fn (Builder $query, $date): Builder => $query->whereDate('plan_date', '<=', $date),
                            );
                    }),

                // Filter theo ngày hạ hàng
                Filter::make('arrival_date_range')
                    ->form([
                        DatePicker::make('arrival_date_from')
                            ->label('Từ ngày hạ hàng'),
                        DatePicker::make('arrival_date_to')
                            ->label('Đến ngày hạ hàng'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['arrival_date_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('arrival_date', '>=', $date),
                            )
                            ->when(
                                $data['arrival_date_to'],
                                fn (Builder $query, $date): Builder => $query->whereDate('arrival_date', '<=', $date),
                            );
                    }),

                // Filter theo ngày xuất kho
                Filter::make('departure_time_range')
                    ->form([
                        DatePicker::make('departure_time_from')
                            ->label('Từ thời gian đóng hàng'),
                        DatePicker::make('departure_time_to')
                            ->label('Đến thời gian đóng hàng'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['departure_time_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('departure_time', '>=', $date),
                            )
                            ->when(
                                $data['departure_time_to'],
                                fn (Builder $query, $date): Builder => $query->whereDate('departure_time', '<=', $date),
                            );
                    }),

                // Filter theo trọng lượng
                Filter::make('weight_range')
                    ->form([
                        Select::make('weight_operator')
                            ->label('Điều kiện')
                            ->options([
                                '>=' => 'Lớn hơn hoặc bằng',
                                '<=' => 'Nhỏ hơn hoặc bằng',
                                '=' => 'Bằng',
                            ])
                            ->default('>='),
                        \Filament\Forms\Components\TextInput::make('weight_value')
                            ->label('Trọng lượng (KG)')
                            ->numeric()
                            ->minValue(0),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (!empty($data['weight_value']) && !empty($data['weight_operator'])) {
                            return $query->where('crate_gross_weight', $data['weight_operator'], $data['weight_value']);
                        }
                        return $query;
                    }),

                // Filter theo số kiện
                Filter::make('pcs_range')
                    ->form([
                        Select::make('pcs_operator')
                            ->label('Điều kiện')
                            ->options([
                                '>=' => 'Lớn hơn hoặc bằng',
                                '<=' => 'Nhỏ hơn hoặc bằng',
                                '=' => 'Bằng',
                            ])
                            ->default('>='),
                        \Filament\Forms\Components\TextInput::make('pcs_value')
                            ->label('Số kiện (PCS)')
                            ->numeric()
                            ->minValue(0),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (!empty($data['pcs_value']) && !empty($data['pcs_operator'])) {
                            return $query->where('crate_pcs', $data['pcs_operator'], $data['pcs_value']);
                        }
                        return $query;
                    }),

                // Filter chỉ hiển thị những record có thông tin xuất kho
                Filter::make('has_shipping_info')
                    ->label('Có thông tin xuất kho')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('shipping_request_id'))
                    ->toggle(),

                // Filter chỉ hiển thị những record chưa có thông tin xuất kho
                Filter::make('no_shipping_info')
                    ->label('Chưa có thông tin xuất kho')
                    ->query(fn (Builder $query): Builder => $query->whereNull('shipping_request_id'))
                    ->toggle(),
            ])
            ->groups([
                Group::make('plan_code')
                ->label('Mã kế hoạch')
                ->collapsible(),
            ])
            ->recordActions([
                // ...
            ])
            ->toolbarActions([
                // ...
            ])
    
            ->paginated([10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(25)
            ->reorderableColumns();
    }
}
