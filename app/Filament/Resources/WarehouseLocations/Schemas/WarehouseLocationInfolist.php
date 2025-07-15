<?php

namespace App\Filament\Resources\WarehouseLocations\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class WarehouseLocationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                \Filament\Schemas\Components\Section::make('Thông tin vị trí kho')
                    ->schema([
                        \Filament\Schemas\Components\Grid::make(2)
                            ->schema([
                                TextEntry::make('location_code')
                                    ->label('Mã vị trí')
                                    ->copyable()
                                    ->icon('heroicon-o-hashtag')
                                    ->badge()
                                    ->color('primary'),
                                TextEntry::make('zone')
                                    ->label('Khu vực')
                                    ->icon('heroicon-o-map'),
                                TextEntry::make('rack')
                                    ->label('Giá kệ')
                                    ->icon('heroicon-o-archive-box'),
                                TextEntry::make('level')
                                    ->label('Tầng')
                                    ->numeric()
                                    ->icon('heroicon-o-bars-3'),
                                TextEntry::make('position')
                                    ->label('Vị trí')
                                    ->icon('heroicon-o-arrow-trending-up'),
                                TextEntry::make('max_weight')
                                    ->label('Trọng lượng tối đa')
                                    ->numeric()
                                    ->suffix(' kg')
                                    ->icon('heroicon-o-scale')
                                    ->color('success'),
                                TextEntry::make('max_volume')
                                    ->label('Thể tích tối đa')
                                    ->numeric()
                                    ->suffix(' m³')
                                    ->icon('heroicon-o-cube')
                                    ->color('info'),
                            ]),
                    ])
                    ->columnSpan(1),

                \Filament\Schemas\Components\Section::make('Trạng thái & hệ thống')
                    ->schema([
                        TextEntry::make('status')
                            ->label('Trạng thái')
                            ->badge()
                            ->color(fn($state) => $state instanceof \App\Enums\WarehouseLocationStatus ? $state->getColor() : \App\Enums\WarehouseLocationStatus::tryFrom($state)?->getColor() ?? 'gray')
                            ->icon(fn($state) => $state instanceof \App\Enums\WarehouseLocationStatus ? $state->getIcon() : \App\Enums\WarehouseLocationStatus::tryFrom($state)?->getIcon())
                            ->formatStateUsing(fn($state) => $state instanceof \App\Enums\WarehouseLocationStatus ? $state->getLabel() : \App\Enums\WarehouseLocationStatus::tryFrom($state)?->getLabel() ?? $state),
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
                    ])
                    ->columnSpan(1)
                    ->collapsible(),
            ]);
    }
}
