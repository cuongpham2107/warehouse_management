<?php

namespace App\Filament\Resources\Crates\Schemas;

use App\Enums\CrateStatus;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class CrateInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Thông tin cơ bản')
                    ->description('Thông tin định danh và kế hoạch nhập kho')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('crate_id')
                                    ->label('Mã thùng hàng')
                                    ->copyable()
                                    ->icon('heroicon-m-clipboard-document-list')
                                    ->badge()
                                    ->color('primary'),
                                TextEntry::make('receivingPlan.plan_code')
                                    ->label('Kế hoạch nhập kho')
                                    ->icon('heroicon-m-archive-box')
                                    ->copyable(),
                            ]),
                    ]),

                Section::make('Thông tin bổ sung')
                    ->description('Mã vạch và mô tả chi tiết')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('barcode')
                                    ->label('Mã vạch')
                                    ->copyable()
                                    ->placeholder('—')
                                    ->icon('heroicon-m-qr-code'),
                                TextEntry::make('description')
                                    ->label('Mô tả')
                                    ->icon('heroicon-m-document-text')
                                    ->placeholder('Không có mô tả'),
                            ]),
                    ]),

                Section::make('Số lượng, trọng lượng và trạng thái')
                    ->description('Số lượng, trọng lượng và trạng thái hiện tại')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextEntry::make('pieces')
                                    ->label('Số lượng')
                                    ->numeric()
                                    ->suffix(' pcs')
                                    ->icon('heroicon-m-cube'),
                                TextEntry::make('type')
                                    ->label('Loại thùng hàng')
                                    ->badge(),
                                TextEntry::make('gross_weight')
                                    ->label('Trọng lượng')
                                    ->numeric(decimalPlaces: 2)
                                    ->suffix(' kg')
                                    ->placeholder('—')
                                    ->icon('heroicon-m-scale'),
                                TextEntry::make('status')
                                    ->label('Trạng thái')
                                    ->badge()
                                    ->color(fn ($state) => $state instanceof CrateStatus ? $state->getColor() : 'gray')
                                    ->icon(fn ($state) => $state instanceof CrateStatus ? $state->getIcon() : 'heroicon-m-question-mark-circle')
                                    ->formatStateUsing(fn ($state) => $state instanceof CrateStatus ? $state->getLabel() : ($state ?? 'N/A')),
                            ]),
                    ]),

                Section::make('Kích thước')
                    ->description('Thông tin kích thước của thùng hàng')
                    ->schema([
                        Grid::make(3)
                            ->schema([
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
                    ]),

                Section::make('Thông tin hệ thống')
                    ->description('Ngày tạo, cập nhật')
                    ->schema([
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
                    ->collapsible(),
            ]);
    }
}
