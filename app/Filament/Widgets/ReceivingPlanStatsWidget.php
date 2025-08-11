<?php

namespace App\Filament\Widgets;

use App\Models\ReceivingPlan;
use App\Models\Vendor;
use App\Enums\ReceivingPlanStatus;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class ReceivingPlanStatsWidget extends BaseWidget
{
    protected static ?string $heading = 'Kế hoạch nhập kho theo nhà cung cấp';

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Vendor::query()
                    ->whereHas('receivingPlans')
                    ->withCount([
                        'receivingPlans',
                        'receivingPlans as pending_plans_count' => function (Builder $query) {
                            $query->where('status', ReceivingPlanStatus::PENDING);
                        },
                        'receivingPlans as in_progress_plans_count' => function (Builder $query) {
                            $query->where('status', ReceivingPlanStatus::IN_PROGRESS);
                        },
                        'receivingPlans as completed_plans_count' => function (Builder $query) {
                            $query->where('status', ReceivingPlanStatus::COMPLETED);
                        },
                        'receivingPlans as cancelled_plans_count' => function (Builder $query) {
                            $query->where('status', ReceivingPlanStatus::CANCELLED);
                        },
                    ])
                    ->withSum('receivingPlans', 'total_crates')
                    ->withSum('receivingPlans', 'total_pcs')
                    ->withSum('receivingPlans', 'total_weight')
                    ->orderBy('receiving_plans_count', 'desc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('vendor_code')
                    ->label('Mã NCC')
                    ->searchable(),

                Tables\Columns\TextColumn::make('vendor_name')
                    ->label('Tên nhà cung cấp')
                    ->searchable()
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('receiving_plans_count')
                    ->label('Tổng kế hoạch')
                    ->alignCenter()
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('pending_plans_count')
                    ->label('Chờ thực hiện')
                    ->alignCenter()
                    ->badge()
                    ->color('warning')
                    ->formatStateUsing(fn ($state) => $state ?: '0'),

                Tables\Columns\TextColumn::make('in_progress_plans_count')
                    ->label('Đang thực hiện')
                    ->alignCenter()
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn ($state) => $state ?: '0'),

                Tables\Columns\TextColumn::make('completed_plans_count')
                    ->label('Hoàn thành')
                    ->alignCenter()
                    ->badge()
                    ->color('success')
                    ->formatStateUsing(fn ($state) => $state ?: '0'),

                Tables\Columns\TextColumn::make('cancelled_plans_count')
                    ->label('Đã hủy')
                    ->alignCenter()
                    ->badge()
                    ->color('danger')
                    ->formatStateUsing(fn ($state) => $state ?: '0'),

                Tables\Columns\TextColumn::make('receiving_plans_sum_total_crates')
                    ->label('Tổng thùng')
                    ->alignCenter()
                    ->formatStateUsing(fn ($state) => number_format($state ?: 0)),

                Tables\Columns\TextColumn::make('receiving_plans_sum_total_pcs')
                    ->label('Tổng PCS')
                    ->alignCenter()
                    ->formatStateUsing(fn ($state) => number_format($state ?: 0)),

                Tables\Columns\TextColumn::make('receiving_plans_sum_total_weight')
                    ->label('Tổng KL (kg)')
                    ->alignCenter()
                    ->formatStateUsing(fn ($state) => number_format($state ?: 0, 2)),

                Tables\Columns\TextColumn::make('completion_rate')
                    ->label('Tỷ lệ HT (%)')
                    ->alignCenter()
                    ->state(function ($record) {
                        $total = $record->receiving_plans_count ?? 0;
                        $completed = $record->completed_plans_count ?? 0;
                        
                        if ($total == 0) return 0;
                        
                        return round(($completed / $total) * 100, 1);
                    })
                    ->formatStateUsing(fn ($state) => $state . '%')
                    ->badge()
                    ->color(function ($state) {
                        if ($state >= 90) return 'success';
                        if ($state >= 70) return 'warning';
                        if ($state > 0) return 'danger';
                        return 'gray';
                    }),
            ])
            ->filters([
                SelectFilter::make('has_pending_plans')
                    ->label('Có kế hoạch chờ')
                    ->options([
                        'yes' => 'Có',
                        'no' => 'Không',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'] === 'yes',
                            fn (Builder $query) => $query->having('pending_plans_count', '>', 0)
                        )->when(
                            $data['value'] === 'no',
                            fn (Builder $query) => $query->having('pending_plans_count', '=', 0)
                        );
                    }),

                SelectFilter::make('has_in_progress_plans')
                    ->label('Có kế hoạch đang thực hiện')
                    ->options([
                        'yes' => 'Có',
                        'no' => 'Không',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'] === 'yes',
                            fn (Builder $query) => $query->having('in_progress_plans_count', '>', 0)
                        )->when(
                            $data['value'] === 'no',
                            fn (Builder $query) => $query->having('in_progress_plans_count', '=', 0)
                        );
                    }),

                SelectFilter::make('completion_rate_range')
                    ->label('Tỷ lệ hoàn thành')
                    ->options([
                        'high' => 'Cao (≥90%)',
                        'medium' => 'Trung bình (70-89%)',
                        'low' => 'Thấp (<70%)',
                        'zero' => 'Chưa hoàn thành (0%)',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'] === 'high',
                            fn (Builder $query) => $query->havingRaw('(completed_plans_count * 100.0 / receiving_plans_count) >= 90')
                        )->when(
                            $data['value'] === 'medium',
                            fn (Builder $query) => $query->havingRaw('(completed_plans_count * 100.0 / receiving_plans_count) >= 70 AND (completed_plans_count * 100.0 / receiving_plans_count) < 90')
                        )->when(
                            $data['value'] === 'low',
                            fn (Builder $query) => $query->havingRaw('(completed_plans_count * 100.0 / receiving_plans_count) < 70 AND completed_plans_count > 0')
                        )->when(
                            $data['value'] === 'zero',
                            fn (Builder $query) => $query->having('completed_plans_count', '=', 0)
                        );
                    }),

                Filter::make('high_volume_vendors')
                    ->label('Nhà cung cấp khối lượng lớn')
                    ->toggle()
                    ->query(fn (Builder $query): Builder => $query->having('receiving_plans_count', '>=', 10)),

                Filter::make('active_vendors_only')
                    ->label('Chỉ nhà cung cấp đang hoạt động')
                    ->toggle()
                    ->default()
                    ->query(fn (Builder $query): Builder => $query->where('vendors.status', 'active')),

                SelectFilter::make('min_total_plans')
                    ->label('Số kế hoạch tối thiểu')
                    ->options([
                        '1' => '≥ 1 kế hoạch',
                        '5' => '≥ 5 kế hoạch',
                        '10' => '≥ 10 kế hoạch',
                        '20' => '≥ 20 kế hoạch',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            filled($data['value']),
                            fn (Builder $query) => $query->having('receiving_plans_count', '>=', (int) $data['value'])
                        );
                    }),
            ])
            ->striped()
            ->paginated([10, 25, 50]);
    }

    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [10, 25, 50];
    }
}
