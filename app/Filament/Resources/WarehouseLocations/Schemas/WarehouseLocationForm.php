<?php

namespace App\Filament\Resources\WarehouseLocations\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use App\Enums\WarehouseLocationStatus;

class WarehouseLocationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Thông tin vị trí')
                    ->description('Định danh và phân loại vị trí trong kho')
                    ->schema([
                        TextInput::make('location_code')
                            ->label('Mã vị trí')
                            ->required()
                            ->placeholder('Nhập mã vị trí'),
                            
                        TextInput::make('zone')
                            ->label('Khu vực')
                            ->required()
                            ->placeholder('Nhập khu vực'),
                            
                        TextInput::make('rack')
                            ->label('Giá kệ')
                            ->required()
                            ->placeholder('Nhập mã giá kệ'),
                            
                        TextInput::make('level')
                            ->label('Tầng')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->placeholder('Nhập số tầng'),
                            
                        TextInput::make('position')
                            ->label('Vị trí')
                            ->required()
                            ->placeholder('Nhập vị trí cụ thể'),
                    ])
                    ->columns(3)
                    ->collapsible(),
                    
                Section::make('Thông số kỹ thuật')
                    ->description('Giới hạn tải trọng và thể tích')
                    ->schema([
                        TextInput::make('max_weight')
                            ->label('Trọng lượng tối đa (kg)')
                            ->numeric()
                            ->minValue(0)
                            ->step(0.01)
                            ->suffix('kg')
                            ->placeholder('Nhập trọng lượng tối đa'),
                            
                        TextInput::make('max_volume')
                            ->label('Thể tích tối đa (m³)')
                            ->numeric()
                            ->minValue(0)
                            ->step(0.01)
                            ->suffix('m³')
                            ->placeholder('Nhập thể tích tối đa'),
                            
                        Select::make('status')
                            ->label('Trạng thái')
                            ->required()
                            ->options(WarehouseLocationStatus::getOptions())
                            ->default(WarehouseLocationStatus::AVAILABLE->value)
                            ->native(false),
                    ])
                    ->columns(3)
                    ->collapsible(),
            ]);
    }
}
