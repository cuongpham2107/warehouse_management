<?php

namespace App\Filament\Resources\Vehicles\Schemas;

use App\Enums\VehicleType;
use App\Enums\VehicleStatus;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;

class VehicleInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Section::make('Thông tin xe')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('vehicle_code')
                                    ->label('Mã xe')
                                    ->copyable()
                                    ->icon('heroicon-o-hashtag')
                                    ->badge()
                                    ->color('primary'),
                                TextEntry::make('vehicle_type')
                                    ->label('Loại xe')
                                    ->badge()
                                    ->color('info')
                                    ->icon('heroicon-o-truck')
                                    ->formatStateUsing(fn ($state) => $state instanceof VehicleType ? $state->label() : VehicleType::tryFrom($state)?->label() ?? $state),
                                TextEntry::make('license_plate')
                                    ->label('Biển số xe')
                                    ->icon('heroicon-o-identification')
                                    ->copyable(),
                                TextEntry::make('driver_name')
                                    ->label('Tên tài xế')
                                    ->icon('heroicon-o-user')
                                    ->placeholder('Chưa có tài xế'),
                                TextEntry::make('driver_phone')
                                    ->label('SĐT tài xế')
                                    ->icon('heroicon-o-phone')
                                    ->placeholder('Chưa có SĐT')
                                    ->copyable(),
                            ]),
                        TextEntry::make('notes')
                            ->label('Ghi chú')
                            ->icon('heroicon-o-document-text')
                            ->placeholder('Không có ghi chú')
                            ->columnSpanFull(),
                    ])
                    ->columnSpan(2),

                Section::make('Thông số kỹ thuật')
                    ->schema([
                        TextEntry::make('capacity_weight')
                            ->label('Tải trọng')
                            ->icon('heroicon-o-scale')
                            ->numeric()
                            ->suffix(' kg')
                            ->color('success'),
                        TextEntry::make('capacity_volume')
                            ->label('Thể tích')
                            ->icon('heroicon-o-cube')
                            ->numeric()
                            ->suffix(' m³')
                            ->color('info'),
                    ])
                    ->columnSpan(1),

                Section::make('Thông tin hệ thống')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('status')
                                    ->label('Trạng thái')
                                    ->badge()
                                    ->color(fn ($state) => $state instanceof VehicleStatus ? $state->color() : VehicleStatus::tryFrom($state)?->color() ?? 'gray')
                                    ->icon(fn ($state) => $state instanceof VehicleStatus ? $state->icon() : VehicleStatus::tryFrom($state)?->icon())
                                    ->formatStateUsing(fn ($state) => $state instanceof VehicleStatus ? $state->label() : VehicleStatus::tryFrom($state)?->label() ?? $state),
                                TextEntry::make('created_at')
                                    ->label('Ngày tạo')
                                    ->icon('heroicon-o-calendar')
                                    ->dateTime('d/m/Y H:i')
                                    ->badge()
                                    ->color('gray'),
                                TextEntry::make('updated_at')
                                    ->label('Cập nhật cuối')
                                    ->icon('heroicon-o-arrow-path')
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
