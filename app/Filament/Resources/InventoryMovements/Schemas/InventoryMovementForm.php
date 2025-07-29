<?php

namespace App\Filament\Resources\InventoryMovements\Schemas;


use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use App\Models\WarehouseLocation;

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
                                        $set('from_location_code', $pallet->location_code ?? null);
                                    })
                                    ->placeholder('Chọn pallet'),
                                TextInput::make('from_location_code')
                                    ->label('Từ vị trí')
                                    ->disabled()
                                    ->dehydrated()
                                    ->placeholder('Vị trí sẽ tự động điền'),
                                TextInput::make('to_location_code')
                                    ->label('Đến vị trí')
                                    ->placeholder('Chọn vị trí đích')
                                    ->datalist(
                                        fn () => WarehouseLocation::query()->pluck('location_code')->all()
                                    ),
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
                                        'transfer' => 'Chuyển kho',
                                        'relocate' => 'Di chuyển vị trí',
                                    ])
                                    ->native(false)
                                    ->placeholder('Chọn loại di chuyển'),
                                DateTimePicker::make('movement_date')
                                    ->label('Ngày di chuyển')
                                    ->default(now())
                                    ->date('d/m/Y H:i')
                                    ->required()
                                    ->placeholder('Chọn ngày di chuyển'),
                                Select::make('device_type')
                                    ->label('Loại thiết bị')
                                    ->required()
                                    ->options([
                                        'scanner' => 'Máy quét',
                                        'manual' => 'Thủ công',
                                    ])
                                    ->default('manual')
                                    ->native(false)
                                    ->placeholder('Chọn loại thiết bị'),
                                   
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
