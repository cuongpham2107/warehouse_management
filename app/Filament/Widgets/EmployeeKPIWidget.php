<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\PalletActivity;
use App\Enums\PalletActivityAction;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class EmployeeKPIWidget extends BaseWidget
{
    protected static ?string $heading = 'KPI Nhân viên theo hoạt động Pallet';

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::query()
                    ->whereHas('palletActivities', function (Builder $query) {
                        $query->whereMonth('action_time', now()->month)
                              ->whereYear('action_time', now()->year);
                    })
                    ->withCount([
                        'palletActivities as total_activities_count' => function (Builder $query) {
                            $query->whereMonth('action_time', now()->month)
                                  ->whereYear('action_time', now()->year);
                        },
                        'palletActivities as import_activities_count' => function (Builder $query) {
                            $query->where('action', PalletActivityAction::IMPORT_PALLET)
                                  ->whereMonth('action_time', now()->month)
                                  ->whereYear('action_time', now()->year);
                        },
                        'palletActivities as export_activities_count' => function (Builder $query) {
                            $query->where('action', PalletActivityAction::EXPORT_PALLET)
                                  ->whereMonth('action_time', now()->month)
                                  ->whereYear('action_time', now()->year);
                        },
                        'palletActivities as relocate_activities_count' => function (Builder $query) {
                            $query->where('action', PalletActivityAction::RELOCATE_PALLET)
                                  ->whereMonth('action_time', now()->month)
                                  ->whereYear('action_time', now()->year);
                        },
                        'palletActivities as attach_crate_activities_count' => function (Builder $query) {
                            $query->where('action', PalletActivityAction::ATTACH_CRATE)
                                  ->whereMonth('action_time', now()->month)
                                  ->whereYear('action_time', now()->year);
                        },
                    ])
                    ->orderBy('total_activities_count', 'desc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('asgl_id')
                    ->label('Mã NV')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Tên nhân viên')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('total_activities_count')
                    ->label('Tổng hoạt động')
                    ->alignCenter()
                    ->badge()
                    ->color('gray')
                    ->sortable(),

                Tables\Columns\TextColumn::make('import_activities_count')
                    ->label('Nhập kho')
                    ->alignCenter()
                    ->badge()
                    ->color('primary')
                    ->formatStateUsing(fn ($state) => $state ?: '0')
                    ->sortable(),

                Tables\Columns\TextColumn::make('export_activities_count')
                    ->label('Xuất kho')
                    ->alignCenter()
                    ->badge()
                    ->color('danger')
                    ->formatStateUsing(fn ($state) => $state ?: '0')
                    ->sortable(),

                Tables\Columns\TextColumn::make('relocate_activities_count')
                    ->label('Chuyển vị trí')
                    ->alignCenter()
                    ->badge()
                    ->color('warning')
                    ->formatStateUsing(fn ($state) => $state ?: '0')
                    ->sortable(),

                Tables\Columns\TextColumn::make('attach_crate_activities_count')
                    ->label('Gắn crate')
                    ->alignCenter()
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn ($state) => $state ?: '0')
                    ->sortable(),

                Tables\Columns\TextColumn::make('daily_average')
                    ->label('TB/ngày')
                    ->alignCenter()
                    ->state(function ($record) {
                        $totalActivities = $record->total_activities_count ?? 0;
                        $workingDays = $this->getWorkingDaysInCurrentMonth();
                        
                        if ($workingDays == 0) return 0;
                        
                        return round($totalActivities / $workingDays, 1);
                    })
                    ->formatStateUsing(fn ($state) => number_format($state, 1))
                    ->badge()
                    ->color(function ($state) {
                        if ($state >= 20) return 'success';
                        if ($state >= 10) return 'warning';
                        if ($state > 0) return 'danger';
                        return 'gray';
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('productivity_score')
                    ->label('Điểm hiệu suất')
                    ->alignCenter()
                    ->state(function ($record) {
                        $import = $record->import_activities_count ?? 0;
                        $export = $record->export_activities_count ?? 0;
                        $relocate = $record->relocate_activities_count ?? 0;
                        $attach = $record->attach_crate_activities_count ?? 0;
                        
                        // Tính điểm dựa trên trọng số của từng hoạt động
                        $score = ($import * 3) + ($export * 3) + ($relocate * 2) + ($attach * 1);
                        
                        return $score;
                    })
                    ->formatStateUsing(fn ($state) => number_format($state))
                    ->badge()
                    ->color(function ($state) {
                        if ($state >= 200) return 'success';
                        if ($state >= 100) return 'warning';
                        if ($state > 0) return 'danger';
                        return 'gray';
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('first_activity_date')
                    ->label('Hoạt động đầu')
                    ->alignCenter()
                    ->state(function ($record) {
                        $firstActivity = PalletActivity::where('user_id', $record->id)
                            ->whereMonth('action_time', now()->month)
                            ->whereYear('action_time', now()->year)
                            ->orderBy('action_time', 'asc')
                            ->first();
                            
                        return $firstActivity ? $firstActivity->action_time->format('d/m') : null;
                    })
                    ->formatStateUsing(fn ($state) => $state ?: '-'),

                Tables\Columns\TextColumn::make('last_activity_date')
                    ->label('Hoạt động cuối')
                    ->alignCenter()
                    ->state(function ($record) {
                        $lastActivity = PalletActivity::where('user_id', $record->id)
                            ->whereMonth('action_time', now()->month)
                            ->whereYear('action_time', now()->year)
                            ->orderBy('action_time', 'desc')
                            ->first();
                            
                        return $lastActivity ? $lastActivity->action_time->format('d/m') : null;
                    })
                    ->formatStateUsing(fn ($state) => $state ?: '-'),

                Tables\Columns\TextColumn::make('active_days')
                    ->label('Số ngày làm')
                    ->alignCenter()
                    ->state(function ($record) {
                        $activeDays = PalletActivity::where('user_id', $record->id)
                            ->whereMonth('action_time', now()->month)
                            ->whereYear('action_time', now()->year)
                            ->selectRaw('COUNT(DISTINCT DATE(action_time)) as active_days')
                            ->first();
                            
                        return $activeDays ? $activeDays->active_days : 0;
                    })
                    ->formatStateUsing(fn ($state) => $state ?: '0')
                    ->badge()
                    ->color(function ($state) {
                        $workingDays = $this->getWorkingDaysInCurrentMonth();
                        $percentage = $workingDays > 0 ? ($state / $workingDays) * 100 : 0;
                        
                        if ($percentage >= 90) return 'success';
                        if ($percentage >= 70) return 'warning';
                        if ($percentage > 0) return 'danger';
                        return 'gray';
                    }),
            ])
            ->filters([
                SelectFilter::make('performance_level')
                    ->label('Mức hiệu suất')
                    ->options([
                        'high' => 'Cao (≥20 hoạt động/ngày)',
                        'medium' => 'Trung bình (10-19 hoạt động/ngày)',
                        'low' => 'Thấp (<10 hoạt động/ngày)',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $workingDays = $this->getWorkingDaysInCurrentMonth();
                        
                        return $query->when(
                            $data['value'] === 'high',
                            fn (Builder $query) => $query->havingRaw('total_activities_count / ? >= 20', [$workingDays])
                        )->when(
                            $data['value'] === 'medium',
                            fn (Builder $query) => $query->havingRaw('total_activities_count / ? >= 10 AND total_activities_count / ? < 20', [$workingDays, $workingDays])
                        )->when(
                            $data['value'] === 'low',
                            fn (Builder $query) => $query->havingRaw('total_activities_count / ? < 10', [$workingDays])
                        );
                    }),

                SelectFilter::make('activity_type_focus')
                    ->label('Chuyên môn theo hoạt động')
                    ->options([
                        'import_expert' => 'Chuyên nhập kho',
                        'export_expert' => 'Chuyên xuất kho',
                        'relocate_expert' => 'Chuyên chuyển vị trí',
                        'balanced' => 'Làm việc đa dạng',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'] === 'import_expert',
                            fn (Builder $query) => $query->havingRaw('import_activities_count >= total_activities_count * 0.6')
                        )->when(
                            $data['value'] === 'export_expert',
                            fn (Builder $query) => $query->havingRaw('export_activities_count >= total_activities_count * 0.6')
                        )->when(
                            $data['value'] === 'relocate_expert',
                            fn (Builder $query) => $query->havingRaw('relocate_activities_count >= total_activities_count * 0.6')
                        )->when(
                            $data['value'] === 'balanced',
                            fn (Builder $query) => $query->havingRaw('import_activities_count < total_activities_count * 0.6')
                                ->havingRaw('export_activities_count < total_activities_count * 0.6')
                                ->havingRaw('relocate_activities_count < total_activities_count * 0.6')
                        );
                    }),

                Filter::make('high_performers_only')
                    ->label('Chỉ nhân viên hiệu suất cao')
                    ->toggle()
                    ->query(function (Builder $query): Builder {
                        $workingDays = $this->getWorkingDaysInCurrentMonth();
                        return $query->havingRaw('total_activities_count / ? >= 15', [$workingDays]);
                    }),

                Filter::make('active_this_week')
                    ->label('Hoạt động tuần này')
                    ->toggle()
                    ->query(function (Builder $query): Builder {
                        return $query->whereHas('palletActivities', function (Builder $subQuery) {
                            $subQuery->whereBetween('action_time', [
                                now()->startOfWeek(),
                                now()->endOfWeek()
                            ]);
                        });
                    }),

                SelectFilter::make('min_activities')
                    ->label('Số hoạt động tối thiểu')
                    ->options([
                        '10' => '≥ 10 hoạt động',
                        '50' => '≥ 50 hoạt động',
                        '100' => '≥ 100 hoạt động',
                        '200' => '≥ 200 hoạt động',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            filled($data['value']),
                            fn (Builder $query) => $query->having('total_activities_count', '>=', (int) $data['value'])
                        );
                    }),
            ])
            ->defaultSort('total_activities_count', 'desc')
            ->striped()
            ->paginated([10, 25, 50])
            ->poll('60s'); // Tự động cập nhật mỗi 60 giây
    }

    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [10, 25, 50];
    }

    /**
     * Tính số ngày làm việc trong tháng hiện tại (trừ chủ nhật)
     */
    protected function getWorkingDaysInCurrentMonth(): int
    {
        return $this->getWorkingDaysInMonth(now()->month, now()->year);
    }

    /**
     * Tính số ngày làm việc trong tháng cụ thể (trừ chủ nhật)
     */
    protected function getWorkingDaysInMonth(int $month, int $year): int
    {
        $start = Carbon::create($year, $month, 1)->startOfMonth();
        $end = Carbon::create($year, $month, 1)->endOfMonth();
        $workingDays = 0;

        while ($start <= $end) {
            if ($start->dayOfWeek !== Carbon::SUNDAY) {
                $workingDays++;
            }
            $start->addDay();
        }

        return $workingDays;
    }

    /**
     * Override để hiển thị thông tin tháng hiện tại trong heading
     */
    public function getHeading(): string
    {
        return 'KPI Nhân viên theo hoạt động Pallet - Tháng ' . now()->format('m/Y');
    }
}
