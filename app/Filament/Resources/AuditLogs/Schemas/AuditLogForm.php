<?php

namespace App\Filament\Resources\AuditLogs\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AuditLogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Thông tin hành động')
                    ->description('Chi tiết về hành động được thực hiện')
                    ->schema([
                        Select::make('user_id')
                            ->label('Người dùng')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder('Chọn người dùng'),
                            
                        Select::make('action')
                            ->label('Hành động')
                            ->required()
                            ->options([
                                'create' => 'Tạo mới',
                                'update' => 'Cập nhật',
                                'delete' => 'Xóa',
                                'view' => 'Xem',
                                'login' => 'Đăng nhập',
                                'logout' => 'Đăng xuất',
                            ])
                            ->placeholder('Chọn loại hành động'),
                            
                        TextInput::make('table_name')
                            ->label('Tên bảng')
                            ->required()
                            ->placeholder('Nhập tên bảng'),
                            
                        TextInput::make('record_id')
                            ->label('ID bản ghi')
                            ->numeric()
                            ->placeholder('Nhập ID bản ghi'),
                    ])
                    ->columns(2)
                    ->collapsible(),
                    
                Section::make('Dữ liệu thay đổi')
                    ->description('Giá trị trước và sau khi thay đổi')
                    ->schema([
                        Textarea::make('old_values')
                            ->label('Giá trị cũ')
                            ->rows(4)
                            ->placeholder('Dữ liệu trước khi thay đổi (JSON format)'),
                            
                        Textarea::make('new_values')
                            ->label('Giá trị mới')
                            ->rows(4)
                            ->placeholder('Dữ liệu sau khi thay đổi (JSON format)'),
                    ])
                    ->columns(2)
                    ->collapsible(),
                    
                Section::make('Thông tin kỹ thuật')
                    ->description('Chi tiết kỹ thuật về phiên làm việc')
                    ->schema([
                        TextInput::make('ip_address')
                            ->label('Địa chỉ IP')
                            ->placeholder('Nhập địa chỉ IP'),
                            
                        Textarea::make('user_agent')
                            ->label('User Agent')
                            ->rows(2)
                            ->placeholder('Thông tin trình duyệt/thiết bị'),
                    ])
                    ->columns(1)
                    ->collapsible(),
            ]);
    }
}
