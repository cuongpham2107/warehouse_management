<?php

namespace App\Filament\Resources\Crates\Schemas;

use App\Enums\CrateStatus;
use App\Enums\PackingType;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class CrateInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Section::make('Thông tin chính')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('crate_id')
                                    ->label('Mã thùng hàng')
                                    ->copyable()
                                    ->icon('heroicon-m-clipboard-document-list')
                                    ->badge()
                                    ->color('primary'),

                                TextEntry::make('status')
                                    ->label('Trạng thái')
                                    ->badge()
                                    ->color(fn ($state) => $state instanceof CrateStatus ? $state->getColor() : 'gray')
                                    ->icon(fn ($state) => $state instanceof CrateStatus ? $state->getIcon() : 'heroicon-m-question-mark-circle')
                                    ->formatStateUsing(fn ($state) => $state instanceof CrateStatus ? $state->getLabel() : ($state ?? 'N/A')),

                                TextEntry::make('receivingPlan.plan_code')
                                    ->label('Kế hoạch nhập kho')
                                    ->icon('heroicon-m-archive-box')
                                    ->copyable(),

                                TextEntry::make('type')
                                    ->label('Loại đóng gói')
                                    ->badge()
                                    ->color(fn ($state) => $state instanceof PackingType ? $state->getColor() : 'gray')
                                    ->icon(fn ($state) => $state instanceof PackingType ? $state->getIcon() : 'heroicon-m-cube')
                                    ->formatStateUsing(fn ($state) => $state instanceof PackingType ? $state->getLabel() : ($state ?? 'N/A')),

                                TextEntry::make('pieces')
                                    ->label('Số lượng')
                                    ->numeric()
                                    ->suffix(' pcs')
                                    ->icon('heroicon-m-cube'),

                                TextEntry::make('gross_weight')
                                    ->label('Trọng lượng')
                                    ->numeric(decimalPlaces: 2)
                                    ->suffix(' kg')
                                    ->placeholder('—')
                                    ->icon('heroicon-m-scale'),

                                TextEntry::make('dimensions_length')
                                    ->label('Chiều dài')
                                    ->numeric(decimalPlaces: 1)
                                    ->suffix(' cm')
                                    ->placeholder('—')
                                    ->icon('heroicon-m-arrows-pointing-out'),

                                TextEntry::make('dimensions_width')
                                    ->label('Chiều rộng')
                                    ->numeric(decimalPlaces: 1)
                                    ->suffix(' cm')
                                    ->placeholder('—')
                                    ->icon('heroicon-m-arrows-pointing-out'),

                                TextEntry::make('dimensions_height')
                                    ->label('Chiều cao')
                                    ->numeric(decimalPlaces: 1)
                                    ->suffix(' cm')
                                    ->placeholder('—')
                                    ->icon('heroicon-m-arrows-pointing-out'),
                            ]),

                        TextEntry::make('description')
                            ->label('Mô tả')
                            ->icon('heroicon-m-document-text')
                            ->placeholder('Không có mô tả')
                            ->columnSpanFull(),
                    ])
                    ->columnSpan(2),

                Section::make('Thông tin mã vạch & hệ thống')
                    ->schema([
                        TextEntry::make('barcode')
                            ->label('Mã vạch')
                            ->copyable()
                            ->placeholder('—')
                            ->icon('heroicon-m-qr-code'),

                        TextEntry::make('created_at')
                            ->label('Ngày tạo')
                            ->dateTime('d/m/Y H:i')
                            ->since()
                            ->tooltip(fn ($state) => $state?->format('d/m/Y H:i:s'))
                            ->icon('heroicon-m-clock'),

                        TextEntry::make('updated_at')
                            ->label('Ngày cập nhật')
                            ->dateTime('d/m/Y H:i')
                            ->since()
                            ->tooltip(fn ($state) => $state?->format('d/m/Y H:i:s'))
                            ->icon('heroicon-m-arrow-path'),
                    ])
                    ->columnSpan(1)
                    ->collapsible(),
            ]);
    }
}
