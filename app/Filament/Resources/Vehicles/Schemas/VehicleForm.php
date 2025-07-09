<?php

namespace App\Filament\Resources\Vehicles\Schemas;

use App\Enums\VehicleType;
use App\Enums\VehicleStatus;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class VehicleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Thông tin xe')
                    ->description('Thông tin cơ bản về phương tiện')
                    ->schema([
                        TextInput::make('vehicle_code')
                            ->label('Mã xe')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->placeholder('Nhập mã xe'),
                            
                        Select::make('vehicle_type')
                            ->label('Loại xe')
                            ->required()
                            ->options(VehicleType::options())
                            ->searchable()
                            ->native(false)
                            ->helperText('Chọn loại phương tiện phù hợp'),
                            
                        TextInput::make('license_plate')
                            ->label('Biển số xe')
                            ->required()
                            ->placeholder('Nhập biển số xe'),
                            
                        Select::make('status')
                            ->label('Trạng thái')
                            ->required()
                            ->options(VehicleStatus::options())
                            ->default(VehicleStatus::AVAILABLE->value)
                            ->native(false)
                            ->helperText('Trạng thái hiện tại của phương tiện'),
                    ])
                    ->columns(2)
                    ->collapsible(),
                    
                Section::make('Thông tin tài xế')
                    ->description('Thông tin người điều khiển phương tiện')
                    ->schema([
                        TextInput::make('driver_name')
                            ->label('Tên tài xế')
                            ->placeholder('Nhập tên tài xế'),
                            
                        TextInput::make('driver_phone')
                            ->label('SĐT tài xế')
                            ->tel()
                            ->placeholder('Nhập số điện thoại'),
                    ])
                    ->columns(2)
                    ->collapsible(),
                    
                Section::make('Thông số kỹ thuật')
                    ->description('Thông số tải trọng và thể tích')
                    ->schema([
                        TextInput::make('capacity_weight')
                            ->label('Tải trọng (kg)')
                            ->numeric()
                            ->minValue(0)
                            ->step(0.01)
                            ->suffix('kg')
                            ->placeholder('Nhập tải trọng tối đa'),
                            
                        TextInput::make('capacity_volume')
                            ->label('Thể tích (m³)')
                            ->numeric()
                            ->minValue(0)
                            ->step(0.01)
                            ->suffix('m³')
                            ->placeholder('Nhập thể tích tối đa'),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }
}
