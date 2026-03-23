<?php

namespace App\Filament\Resources\Vendors\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use App\Enums\CommonStatus;

class VendorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Thông tin cơ bản')
                    ->description('Thông tin định danh nhà cung cấp')
                    ->schema([
                        TextInput::make('vendor_code')
                            ->label('Mã nhà cung cấp')
                            ->required()
                            ->maxLength(50)
                            ->unique(ignoreRecord: true)
                            ->placeholder('Nhập mã nhà cung cấp'),
                            
                        TextInput::make('vendor_name')
                            ->label('Tên nhà cung cấp')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Nhập tên nhà cung cấp'),
                            
                        Select::make('status')
                            ->label('Trạng thái')
                            ->required()
                            ->options(CommonStatus::getOptions())
                            ->default(CommonStatus::ACTIVE->value)
                            ->native(false),
                    ])
                    ->columns(3)
                    ->collapsible(),
                    
                Section::make('Thông tin liên hệ')
                    ->description('Thông tin liên hệ với nhà cung cấp')
                    ->schema([
                        TextInput::make('contact_person')
                            ->label('Người liên hệ')
                            ->maxLength(255)
                            ->placeholder('Nhập tên người liên hệ'),
                            
                        TextInput::make('phone')
                            ->label('Số điện thoại')
                            ->tel()
                            ->maxLength(20)
                            ->placeholder('Nhập số điện thoại'),
                            
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255)
                            ->placeholder('Nhập địa chỉ email'),
                    ])
                    ->columns(3)
                    ->collapsible(),
                    
                Section::make('Địa chỉ')
                    ->description('Địa chỉ chi tiết của nhà cung cấp')
                    ->schema([
                        Textarea::make('address')
                            ->label('Địa chỉ')
                            ->rows(3)
                            ->placeholder('Nhập địa chỉ đầy đủ'),
                    ])
                    ->columns(1)
                    ->collapsible(),
            ]);
    }
}
