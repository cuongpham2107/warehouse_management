<?php

namespace App\Filament\Resources\InventoryMovements\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class InventoryMovementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Thông tin di chuyển')
                    ->description('Thông tin cơ bản về việc di chuyển hàng hóa')
                    ->schema([
                        Select::make('pallet_id')
                            ->label('Pallet')
                            ->relationship('pallet', 'pallet_id')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->placeholder('Chọn pallet'),
                            
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
                    ])
                    ->columns(3)
                    ->collapsible(),
                    
                Section::make('Vị trí')
                    ->description('Vị trí xuất phát và đích đến')
                    ->schema([
                        Select::make('from_location_id')
                            ->label('Từ vị trí')
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
                    ])
                    ->columns(2)
                    ->collapsible(),
                    
                Section::make('Thông tin tham chiếu')
                    ->description('Thông tin liên kết với đơn hàng hoặc yêu cầu')
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
                    ])
                    ->columns(2)
                    ->collapsible(),
                    
                Section::make('Thông tin thiết bị và người thực hiện')
                    ->description('Thiết bị sử dụng và người thực hiện')
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
                            
                        Select::make('performed_by')
                            ->label('Thực hiện bởi')
                            ->relationship('performer', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder('Chọn người thực hiện'),
                    ])
                    ->columns(3)
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
