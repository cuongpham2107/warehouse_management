<?php

namespace App\Filament\Resources\InventoryMovements\Schemas;


use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class InventoryMovementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Thông tin pallet & vị trí')
                    ->description('Chọn pallet và vị trí di chuyển')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Select::make('pallet_id')
                                    ->label('Pallet')
                                    ->relationship('pallet', 'pallet_id')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->live(onBlur:true)
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        $pallet = \App\Models\Pallet::find($state);
                                        $set('from_location_id', $pallet->location_id ?? null);
                                    })
                                    ->placeholder('Chọn pallet'),
                                Select::make('from_location_id')
                                    ->label('Từ vị trí')
                                    ->disabled()
                                    ->dehydrated()
                                    ->relationship('fromLocation', 'location_code')
                                    ->searchable()
                                    ->preload()
                                    ->placeholder('Chọn vị trí xuất phát'),
                                Select::make('to_location_id')
                                    ->label('Đến vị trí')
                                    ->relationship('toLocation', 'location_code')
                                    ->searchable()
                                    ->preload()
                                    ->placeholder('Chọn vị trí đích'),
                            ]),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Section::make('Thông tin di chuyển & thiết bị')
                    ->description('Loại di chuyển, ngày, thiết bị, người thực hiện')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Select::make('movement_type')
                                    ->label('Loại di chuyển')
                                    ->required()
                                    ->options([
                                        'in' => 'Nhập kho',
                                        'out' => 'Xuất kho',
                                        'transfer' => 'Chuyển kho',
                                        'relocate' => 'Di chuyển vị trí',
                                    ])
                                    ->native(false)
                                    ->placeholder('Chọn loại di chuyển'),
                                DateTimePicker::make('movement_date')
                                    ->label('Ngày di chuyển')
                                    ->required()
                                    ->placeholder('Chọn ngày di chuyển'),
                                Grid::make(2)
                                    ->schema([
                                        Select::make('device_type')
                                            ->label('Loại thiết bị')
                                            ->required()
                                            ->options([
                                                'handheld' => 'Máy cầm tay',
                                                'forklift' => 'Xe nâng',
                                                'scanner' => 'Máy quét',
                                                'manual' => 'Thủ công',
                                            ])
                                            ->native(false)
                                            ->placeholder('Chọn loại thiết bị'),
                                        Select::make('device_id')
                                            ->label('Thiết bị')
                                            ->relationship('device', 'device_name')
                                            ->searchable()
                                            ->preload()
                                            ->placeholder('Chọn thiết bị'),
                                    ]),
                            ]),
                        Select::make('performed_by')
                            ->label('Thực hiện bởi')
                            ->relationship('performer', 'name')
                            ->default(\Illuminate\Support\Facades\Auth::user()->id)
                            ->disabled()
                            ->dehydrated()
                            ->searchable()
                            ->preload()
                            ->placeholder('Chọn người thực hiện'),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Section::make('Tham chiếu')
                    ->description('Thông tin liên kết')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('reference_type')
                                    ->label('Loại tham chiếu')
                                    ->required()
                                    ->options([
                                        'shipment' => 'Lô hàng',
                                        'receiving_plan' => 'Kế hoạch nhập kho',
                                        'shipping_request' => 'Yêu cầu vận chuyển',
                                        'manual' => 'Thủ công',
                                    ])
                                    ->native(false)
                                    ->placeholder('Chọn loại tham chiếu'),
                                TextInput::make('reference_id')
                                    ->label('ID tham chiếu')
                                    ->numeric()
                                    ->placeholder('Nhập ID tham chiếu'),
                            ]),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Section::make('Ghi chú')
                    ->description('Thông tin bổ sung về việc di chuyển')
                    ->schema([
                        Textarea::make('notes')
                            ->label('Ghi chú')
                            ->rows(3)
                            ->placeholder('Nhập ghi chú về việc di chuyển'),
                    ])
                    ->columns(1)
                    ->collapsible(),
            ]);
    }
}
