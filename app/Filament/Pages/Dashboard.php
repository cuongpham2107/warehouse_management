<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Actions\Action;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Quản lý kho ASGL';
    
    public function getWidgets(): array
    {
        return [
            // Tổng quan hệ thống
            \App\Filament\Widgets\StatsOverviewWidget::class,
            // Hoạt động nhập/xuất pallet
            \App\Filament\Widgets\WarehouseActivityWidget::class,

            \App\Filament\Widgets\ShippedPalletsWidget::class,
            // Yêu cầu vận chuyển gần đây
            \App\Filament\Widgets\RecentShippingRequestsWidget::class,
        ];
    }
    public function getColumns(): int | array
    {
        return 2;
    }
    public function getHeaderActions(): array
    {
        return [
            Action::make('create_receiving_plan')
                ->label('Tạo mới kế hoạch nhập kho')
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->modal('create_receiving_plan')
                ->modalHeading('Tạo mới kế hoạch nhập kho')
                ->modalDescription('Vui lòng điền thông tin kế hoạch nhập kho.')
                ->schema([
                    \Filament\Schemas\Components\Grid::make(6)
                        ->schema([
                            \Filament\Forms\Components\TextInput::make('plan_code')
                                ->label('Mã kế hoạch')
                                ->default(fn () => 'RP' . now()->format('Ymd') . '-' . str_pad((string) (\App\Models\ReceivingPlan::max('id') + 1), 4, '0', STR_PAD_LEFT))
                                ->helperText('Hệ thống sẽ tự động tạo mã theo format')
                                ->disabled()
                                ->dehydrated()
                                ->columnSpan(3),
                            \Filament\Forms\Components\Select::make('vendor_id')
                                ->label('Nhà cung cấp')
                                ->required()
                                ->options(\App\Models\Vendor::where('status', 'active')->pluck('vendor_name', 'id'))
                                ->searchable()
                                ->preload()
                                ->placeholder('Chọn nhà cung cấp')
                                ->columnSpan(3),
                            \Filament\Forms\Components\DatePicker::make('plan_date')
                                ->label('Ngày kế hoạch')
                                ->required()
                                ->default(now())
                                ->placeholder('Chọn ngày kế hoạch')
                                ->columnSpan(2),
                            \Filament\Forms\Components\Select::make('status')
                                ->label('Trạng thái')
                                ->required()
                                ->options(\App\Enums\ReceivingPlanStatus::getOptions())
                                ->default(\App\Enums\ReceivingPlanStatus::PENDING->value)
                                ->disabled()
                                ->dehydrated()
                                ->columnSpan(2),
                            \Filament\Forms\Components\Select::make('created_by')
                                ->label('Người tạo')
                                ->options(\App\Models\User::pluck('name', 'id'))
                                ->default(fn () => \Illuminate\Support\Facades\Auth::id())
                                ->disabled()
                                ->dehydrated()
                                ->helperText('Tự động gán người dùng hiện tại')
                                ->columnSpan(2),
                            \Filament\Forms\Components\Textarea::make('notes')
                                ->label('Ghi chú')
                                ->rows(3)
                                ->placeholder('Nhập ghi chú về kế hoạch')
                                ->columnSpanFull(),
                        ]),
                ])
                ->action(function(array $data){
                    $receivingPlan = \App\Models\ReceivingPlan::create($data);
                    \Filament\Notifications\Notification::make()
                        ->success()
                        ->title('Tạo kế hoạch thành công')
                        ->body('Kế hoạch nhập kho đã được tạo thành công.')
                        ->actions([
                            \Filament\Actions\Action::make('view_receiving_plan')
                                ->label('Xem kế hoạch')
                                ->url(fn () => route('filament.admin.resources.receiving-plans.edit', ['record' => $receivingPlan->id]))
                                ->icon('heroicon-o-eye'),
                        ])
                        ->send();
                }),
        ];
    }
}