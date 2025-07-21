<?php

namespace App\Filament\Resources\Pallets\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use App\Enums\PalletStatus;
use Filament\Schemas\Components\Flex;

class PalletForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Thông tin cơ bản')
                    ->description('Thông tin định danh và phân loại pallet')
                    ->schema([
                        TextInput::make('pallet_id')
                            ->label('Mã pallet')
                            ->required()
                            ->maxLength(50)
                            ->placeholder('Nhập mã pallet'),

                        Select::make('crate_id')
                            ->label('Thùng hàng')
                            ->required()
                            ->relationship('crate', 'crate_id')
                            ->searchable()
                            ->preload()
                            ->placeholder('Chọn thùng hàng'),

                        TextInput::make('location_code')
                            ->label('Mã vị trí')
                            ->maxLength(50)
                            ->required()
                            ->placeholder('Nhập mã vị trí'),

                        Select::make('status')
                            ->label('Trạng thái')
                            ->required()
                            ->options(PalletStatus::getOptions())
                            ->default(PalletStatus::IN_TRANSIT->value)
                            ->native(false),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Flex::make([
                    Section::make('Thông tin nhập kho')
                        ->description('Chi tiết về việc nhập kho pallet')
                        ->schema([
                            DateTimePicker::make('checked_in_at')
                                ->label('Thời gian nhập kho')
                                ->placeholder('Chọn thời gian nhập kho')
                                ->seconds(false)
                                ->displayFormat('d/m/Y H:i')
                                ->readOnly(),

                            Select::make('checked_in_by')
                                ->label('Người nhập kho')
                                ->relationship('checkedInBy', 'name')
                                ->searchable()
                                ->preload()
                                ->placeholder('Chọn người nhập kho')
                                ->disabled(),
                        ])
                        ->columns(2),

                    Section::make('Thông tin xuất kho')
                        ->description('Chi tiết về việc xuất kho pallet')
                        ->schema([
                            DateTimePicker::make('checked_out_at')
                                ->label('Thời gian xuất kho')
                                ->placeholder('Chọn thời gian xuất kho')
                                ->displayFormat('d/m/Y H:i')
                                ->native(false)
                                ->seconds(false)
                                ->weekStartsOnMonday()
                                ->locale('vi'),
                                // ->readOnly(),

                            Select::make('checked_out_by')
                                ->label('Người xuất kho')
                                ->relationship('checkedOutBy', 'name')
                                ->searchable()
                                ->preload()
                                ->placeholder('Chọn người xuất kho')
                                ->disabled(),
                        ])
                        ->columns(2),
                ])
            ]);
    }
}
