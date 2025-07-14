<?php

namespace App\Filament\Resources\ReceivingPlans\Schemas;

use App\Enums\ReceivingPlanStatus;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class ReceivingPlanInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Section::make('Thông tin chính')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('plan_code')
                                    ->label('Mã kế hoạch')
                                    ->copyable()
                                    ->icon('heroicon-m-clipboard-document-list')
                                    ->badge()
                                    ->color('primary'),

                                TextEntry::make('status')
                                    ->label('Trạng thái')
                                    ->badge()
                                    ->color(fn ($state) => match ($state) {
                                        'pending' => 'warning',
                                        'in_progress' => 'info',
                                        'completed' => 'success',
                                        'cancelled' => 'danger',
                                        default => 'gray',
                                    })
                                    ->formatStateUsing(fn ($state) => match ($state) {
                                        'pending' => 'Chờ xử lý',
                                        'in_progress' => 'Đang thực hiện',
                                        'completed' => 'Hoàn thành',
                                        'cancelled' => 'Đã hủy',
                                        default => $state,
                                    }),

                                TextEntry::make('vendor.vendor_name')
                                    ->label('Nhà cung cấp')
                                    ->icon('heroicon-m-building-office')
                                    ->weight('bold'),

                                TextEntry::make('plan_date')
                                    ->label('Ngày kế hoạch')
                                    ->icon('heroicon-m-calendar-days')
                                    ->date('d/m/Y')
                                    ->badge()
                                    ->color('info'),
                            ]),

                        TextEntry::make('notes')
                            ->label('Ghi chú')
                            ->icon('heroicon-m-document-text')
                            ->placeholder('Không có ghi chú')
                            ->columnSpanFull(),
                    ])
                    ->columnSpan(2),

                Section::make('Thống kê')
                    ->schema([
                        TextEntry::make('total_crates')
                            ->label('Tổng số thùng')
                            ->icon('heroicon-m-archive-box')
                            ->numeric()
                            ->suffix(' thùng')
                            ->color('success'),

                        TextEntry::make('total_pieces')
                            ->label('Tổng số sản phẩm')
                            ->icon('heroicon-m-squares-2x2')
                            ->numeric()
                            ->suffix(' sản phẩm')
                            ->color('info'),

                        TextEntry::make('total_weight')
                            ->label('Tổng khối lượng')
                            ->icon('heroicon-m-scale')
                            ->numeric(decimalPlaces: 2)
                            ->suffix(' kg')
                            ->color('warning'),
                    ])
                    ->columnSpan(1),

                Section::make('Thông tin hệ thống')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('creator.name')
                                    ->label('Người tạo')
                                    ->icon('heroicon-m-user')
                                    ->placeholder('Không xác định'),

                                TextEntry::make('created_at')
                                    ->label('Ngày tạo')
                                    ->icon('heroicon-m-clock')
                                    ->dateTime('d/m/Y H:i')
                                    ->badge()
                                    ->color('gray'),

                                TextEntry::make('updated_at')
                                    ->label('Cập nhật cuối')
                                    ->icon('heroicon-m-arrow-path')
                                    ->dateTime('d/m/Y H:i')
                                    ->since()
                                    ->badge()
                                    ->color('gray'),
                            ]),
                    ])
                    ->columnSpan(3)
                    ->collapsible(),
            ]);
    }
}
