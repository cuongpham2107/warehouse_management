<?php

namespace App\Filament\Widgets;

use App\Models\PalletWithInfo;
use App\Models\Vendor;
use App\Models\VendorStats;
use App\Enums\PalletStatus;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Pagination\Paginator;

class ReceivingPlanStatsWidget extends BaseWidget
{
    protected static ?string $heading = 'Thống kê Pallet theo nhà cung cấp';

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {        
        return $table
            ->query(
                // Sử dụng VendorStats model với join trực tiếp
                VendorStats::query()
                    ->join('pallet_with_info', 'vendors.id', '=', 'pallet_with_info.vendor_id')
                    ->whereNotNull('pallet_with_info.vendor_id')
                    ->select([
                        'vendors.id',
                        'vendors.vendor_name',
                        'vendors.vendor_code',
                        DB::raw('COUNT(*) as total_pallets'),
                        DB::raw('SUM(CASE WHEN pallet_with_info.pallet_status = "stored" THEN 1 ELSE 0 END) as stored_pallets'),
                        DB::raw('SUM(CASE WHEN pallet_with_info.pallet_status = "shipped" THEN 1 ELSE 0 END) as shipped_pallets'),
                        DB::raw('SUM(CASE WHEN pallet_with_info.pallet_status = "in_stock" THEN 1 ELSE 0 END) as in_stock_pallets'),
                        DB::raw('SUM(CASE WHEN pallet_with_info.pallet_status = "in_transit" THEN 1 ELSE 0 END) as in_transit_pallets'),
                        DB::raw('SUM(CASE WHEN pallet_with_info.pallet_status = "damaged" THEN 1 ELSE 0 END) as damaged_pallets'),
                        DB::raw('SUM(COALESCE(pallet_with_info.crate_pcs, 0)) as total_pcs'),
                        DB::raw('SUM(COALESCE(pallet_with_info.crate_gross_weight, 0)) as total_weight'),
                        DB::raw('COUNT(DISTINCT pallet_with_info.plan_code) as total_plans'),
                    ])
                    ->groupBy('vendors.id', 'vendors.vendor_name', 'vendors.vendor_code')
                    ->orderByRaw('COUNT(*) DESC, vendors.vendor_name ASC')
            )
            ->columns([
                Tables\Columns\TextColumn::make('vendor_code')
                    ->label('Mã NCC')
                    ->searchable(false)
                    ->sortable(false),

                Tables\Columns\TextColumn::make('vendor_name')
                    ->label('Nhà cung cấp')
                    ->searchable(false)
                    ->weight('medium')
                    ->sortable(false),

                Tables\Columns\TextColumn::make('total_pallets')
                    ->label('Tổng Pallet')
                    ->alignCenter()
                    ->badge()
                    ->color('gray')
                    ->sortable(false),

                Tables\Columns\TextColumn::make('stored_pallets')
                    ->label('Tồn kho')
                    ->alignCenter()
                    ->badge()
                    ->color('success')
                    ->formatStateUsing(fn ($state) => $state ?: '0')
                    ->sortable(false),

                Tables\Columns\TextColumn::make('shipped_pallets')
                    ->label('Đã xuất')
                    ->alignCenter()
                    ->badge()
                    ->color('primary')
                    ->formatStateUsing(fn ($state) => $state ?: '0')
                    ->sortable(false),

                Tables\Columns\TextColumn::make('in_stock_pallets')
                    ->label('Đang xuất')
                    ->alignCenter()
                    ->badge()
                    ->color('warning')
                    ->formatStateUsing(fn ($state) => $state ?: '0')
                    ->sortable(false),

                Tables\Columns\TextColumn::make('in_transit_pallets')
                    ->label('Vận chuyển')
                    ->alignCenter()
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn ($state) => $state ?: '0')
                    ->sortable(false),

                Tables\Columns\TextColumn::make('damaged_pallets')
                    ->label('Hư hỏng')
                    ->alignCenter()
                    ->badge()
                    ->color('danger')
                    ->formatStateUsing(fn ($state) => $state ?: '0')
                    ->sortable(false),

                Tables\Columns\TextColumn::make('total_pcs')
                    ->label('Tổng PCS')
                    ->alignCenter()
                    ->formatStateUsing(fn ($state) => number_format($state ?: 0))
                    ->sortable(false),

                Tables\Columns\TextColumn::make('total_weight')
                    ->label('Tổng KL (kg)')
                    ->alignCenter()
                    ->formatStateUsing(fn ($state) => number_format($state ?: 0, 2))
                    ->sortable(false),

                Tables\Columns\TextColumn::make('total_plans')
                    ->label('Số kế hoạch')
                    ->alignCenter()
                    ->badge()
                    ->color('gray')
                    ->sortable(false),

                Tables\Columns\TextColumn::make('storage_rate')
                    ->label('Tỷ lệ tồn (%)')
                    ->alignCenter()
                    ->state(function ($record) {
                        $total = $record->total_pallets ?? 0;
                        $stored = $record->stored_pallets ?? 0;
                        
                        if ($total == 0) return 0;
                        
                        return round(($stored / $total) * 100, 1);
                    })
                    ->formatStateUsing(fn ($state) => $state . '%')
                    ->badge()
                    ->color(function ($state) {
                        if ($state >= 70) return 'success';
                        if ($state >= 40) return 'warning';
                        if ($state > 0) return 'info';
                        return 'gray';
                    })
                    ->sortable(false),

                Tables\Columns\TextColumn::make('shipping_rate')
                    ->label('Tỷ lệ xuất (%)')
                    ->alignCenter()
                    ->state(function ($record) {
                        $total = $record->total_pallets ?? 0;
                        $shipped = $record->shipped_pallets ?? 0;
                        
                        if ($total == 0) return 0;
                        
                        return round(($shipped / $total) * 100, 1);
                    })
                    ->formatStateUsing(fn ($state) => $state . '%')
                    ->badge()
                    ->color(function ($state) {
                        if ($state >= 70) return 'primary';
                        if ($state >= 40) return 'warning';
                        if ($state > 0) return 'info';
                        return 'gray';
                    })
                    ->sortable(false),
            ])
            ->filters([
                SelectFilter::make('has_stored_pallets')
                    ->label('Có hàng tồn kho')
                    ->options([
                        'yes' => 'Có',
                        'no' => 'Không',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'] === 'yes',
                            fn (Builder $query) => $query->having('stored_pallets', '>', 0)
                        )->when(
                            $data['value'] === 'no',
                            fn (Builder $query) => $query->having('stored_pallets', '=', 0)
                        );
                    }),

                SelectFilter::make('has_shipped_pallets')
                    ->label('Có hàng đã xuất')
                    ->options([
                        'yes' => 'Có',
                        'no' => 'Không',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'] === 'yes',
                            fn (Builder $query) => $query->having('shipped_pallets', '>', 0)
                        )->when(
                            $data['value'] === 'no',
                            fn (Builder $query) => $query->having('shipped_pallets', '=', 0)
                        );
                    }),

                SelectFilter::make('has_damaged_pallets')
                    ->label('Có hàng hư hỏng')
                    ->options([
                        'yes' => 'Có',
                        'no' => 'Không',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'] === 'yes',
                            fn (Builder $query) => $query->having('damaged_pallets', '>', 0)
                        )->when(
                            $data['value'] === 'no',
                            fn (Builder $query) => $query->having('damaged_pallets', '=', 0)
                        );
                    }),

                SelectFilter::make('storage_rate_range')
                    ->label('Tỷ lệ tồn kho')
                    ->options([
                        'high' => 'Cao (≥70%)',
                        'medium' => 'Trung bình (40-69%)',
                        'low' => 'Thấp (<40%)',
                        'zero' => 'Không tồn (0%)',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'] === 'high',
                            fn (Builder $query) => $query->havingRaw('(stored_pallets * 100.0 / total_pallets) >= 70')
                        )->when(
                            $data['value'] === 'medium',
                            fn (Builder $query) => $query->havingRaw('(stored_pallets * 100.0 / total_pallets) >= 40 AND (stored_pallets * 100.0 / total_pallets) < 70')
                        )->when(
                            $data['value'] === 'low',
                            fn (Builder $query) => $query->havingRaw('(stored_pallets * 100.0 / total_pallets) < 40 AND stored_pallets > 0')
                        )->when(
                            $data['value'] === 'zero',
                            fn (Builder $query) => $query->having('stored_pallets', '=', 0)
                        );
                    }),

                SelectFilter::make('shipping_rate_range')
                    ->label('Tỷ lệ xuất kho')
                    ->options([
                        'high' => 'Cao (≥70%)',
                        'medium' => 'Trung bình (40-69%)',
                        'low' => 'Thấp (<40%)',
                        'zero' => 'Chưa xuất (0%)',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'] === 'high',
                            fn (Builder $query) => $query->havingRaw('(shipped_pallets * 100.0 / total_pallets) >= 70')
                        )->when(
                            $data['value'] === 'medium',
                            fn (Builder $query) => $query->havingRaw('(shipped_pallets * 100.0 / total_pallets) >= 40 AND (shipped_pallets * 100.0 / total_pallets) < 70')
                        )->when(
                            $data['value'] === 'low',
                            fn (Builder $query) => $query->havingRaw('(shipped_pallets * 100.0 / total_pallets) < 40 AND shipped_pallets > 0')
                        )->when(
                            $data['value'] === 'zero',
                            fn (Builder $query) => $query->having('shipped_pallets', '=', 0)
                        );
                    }),

                Filter::make('high_volume_vendors')
                    ->label('Nhà cung cấp khối lượng lớn')
                    ->toggle()
                    ->query(fn (Builder $query): Builder => $query->having('total_pallets', '>=', 10)),

                Filter::make('active_shipping')
                    ->label('Đang có hoạt động xuất kho')
                    ->toggle()
                    ->query(fn (Builder $query): Builder => $query->having('in_stock_pallets', '>', 0)),

                SelectFilter::make('min_total_pallets')
                    ->label('Số pallet tối thiểu')
                    ->options([
                        '1' => '≥ 1 pallet',
                        '5' => '≥ 5 pallet',
                        '10' => '≥ 10 pallet',
                        '20' => '≥ 20 pallet',
                        '50' => '≥ 50 pallet',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            filled($data['value']),
                            fn (Builder $query) => $query->having('total_pallets', '>=', (int) $data['value'])
                        );
                    }),
            ])
            ->groups([
                Group::make('vendor_name')
                    ->label('Theo nhà cung cấp')
                    ->collapsible(),

                Group::make('storage_rate')
                    ->label('Theo tỷ lệ tồn kho')
                    ->getDescriptionFromRecordUsing(function ($record) {
                        $rate = $record->total_pallets > 0 ? round(($record->stored_pallets / $record->total_pallets) * 100, 1) : 0;
                        if ($rate >= 70) return 'Tồn kho cao (≥70%)';
                        if ($rate >= 40) return 'Tồn kho trung bình (40-69%)';
                        if ($rate > 0) return 'Tồn kho thấp (<40%)';
                        return 'Không tồn kho (0%)';
                    })
                    ->collapsible(),

                Group::make('shipping_rate')
                    ->label('Theo tỷ lệ xuất kho')
                    ->getDescriptionFromRecordUsing(function ($record) {
                        $rate = $record->total_pallets > 0 ? round(($record->shipped_pallets / $record->total_pallets) * 100, 1) : 0;
                        if ($rate >= 70) return 'Xuất kho cao (≥70%)';
                        if ($rate >= 40) return 'Xuất kho trung bình (40-69%)';
                        if ($rate > 0) return 'Xuất kho thấp (<40%)';
                        return 'Chưa xuất kho (0%)';
                    })
                    ->collapsible(),

                Group::make('total_pallets')
                    ->label('Theo khối lượng pallet')
                    ->getDescriptionFromRecordUsing(function ($record) {
                        $total = $record->total_pallets;
                        if ($total >= 50) return 'Khối lượng rất lớn (≥50 pallet)';
                        if ($total >= 20) return 'Khối lượng lớn (20-49 pallet)';
                        if ($total >= 10) return 'Khối lượng trung bình (10-19 pallet)';
                        return 'Khối lượng nhỏ (<10 pallet)';
                    })
                    ->collapsible(),
            ])
            ->defaultGroup('vendor_name')
            ->striped()
            ->paginated([25, 50, 100])
            ->reorderable(false)
            ->searchable(false)
            ->defaultSort(null) // Tắt default sorting
            ->deferLoading();
    }

    protected function getDefaultTableSortColumn(): ?string
    {
        return null; // Tắt default sorting
    }

    protected function getDefaultTableSortDirection(): ?string
    {
        return null; // Tắt default sorting
    }

    protected function applyDefaultSortingToTableQuery(Builder $query): Builder
    {
        // Không áp dụng default sorting, giữ nguyên ordering từ query
        return $query;
    }

    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [10, 25, 50];
    }
}
