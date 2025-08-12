<?php

namespace App\Filament\Resources\Devices\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use App\Enums\DeviceStatus;

class DeviceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Thông tin thiết bị')
                    ->description('Thông tin cơ bản về thiết bị')
                    ->schema([
                        TextInput::make('device_code')
                            ->label('Mã thiết bị')
                            ->required()
                            ->placeholder('Nhập mã thiết bị'),
                            
                        TextInput::make('device_type')
                            ->label('Loại thiết bị')
                            ->required()
                            ->placeholder('Nhập loại thiết bị'),
                            
                        TextInput::make('device_name')
                            ->label('Tên thiết bị')
                            ->required()
                            ->placeholder('Nhập tên thiết bị'),
                    ])
                    ->columns(3)
                    ->collapsible(),
                    
                Section::make('Cấu hình mạng')
                    ->description('Thông tin kết nối mạng của thiết bị')
                    ->schema([
                        TextInput::make('mac_address')
                            ->label('Địa chỉ MAC')
                            ->placeholder('Nhập địa chỉ MAC'),
                            
                        TextInput::make('ip_address')
                            ->label('Địa chỉ IP')
                            ->placeholder('Nhập địa chỉ IP'),
                            
                        DateTimePicker::make('last_sync_at')
                            ->label('Lần đồng bộ cuối')
                            ->placeholder('Chọn thời gian đồng bộ'),
                    ])
                    ->columns(3)
                    ->collapsible(),
                    
                Section::make('Trạng thái và phân công')
                    ->description('Trạng thái hoạt động và phân công sử dụng')
                    ->schema([
                        Select::make('status')
                            ->label('Trạng thái')
                            ->required()
                            ->options(DeviceStatus::getOptions())
                            ->default(DeviceStatus::INACTIVE->value),
                            
                        TextInput::make('assigned_to')
                            ->label('Được gán cho')
                            ->numeric()
                            ->placeholder('Nhập ID người dùng'),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }
}
