<?php

namespace App\Filament\Resources\Crates\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use App\Enums\CrateStatus;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;

class CrateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Tabs::make('Tabs')
                    ->tabs([
                        Tab::make('Thông tin cơ bản')
                            // ->description('Thông tin định danh và kế hoạch nhập kho')
                            ->schema([
                                TextInput::make('crate_id')
                                    ->label('Mã thùng hàng')
                                    ->required()
                                    ->maxLength(50)
                                    ->placeholder('Nhập mã thùng hàng'),

                                Select::make('receiving_plan_id')
                                    ->label('Kế hoạch nhập kho')
                                    ->required()
                                    ->relationship('receivingPlan', 'plan_code')
                                    ->searchable()
                                    ->preload()
                                    ->placeholder('Chọn kế hoạch nhập kho'),
                            ])->columns(2),
                        Tab::make('Thông tin bổ sung')
                            // ->description('Mã vạch và mô tả chi tiết')
                            ->schema([
                                TextInput::make('barcode')
                                    ->label('Mã vạch')
                                    ->maxLength(255)
                                    ->placeholder('Nhập mã vạch'),

                                Textarea::make('description')
                                    ->label('Mô tả')
                                    ->rows(3)
                                    ->placeholder('Nhập mô tả thùng hàng'),
                            ])->columns(1),
                    ]),

                Section::make('Thông tin số lượng và trạng thái')
                    ->description('Số lượng, trọng lượng và trạng thái hiện tại')
                    ->schema([
                        TextInput::make('pieces')
                            ->label('Số lượng')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->placeholder('Nhập số lượng'),
                        TextInput::make('type')
                            ->label('Loại thùng hàng')
                            ->required(),
                        TextInput::make('gross_weight')
                            ->label('Trọng lượng (kg)')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->step(0.01)
                            ->suffix('kg')
                            ->placeholder('Nhập trọng lượng'),

                        Select::make('status')
                            ->label('Trạng thái')
                            ->required()
                            ->options(CrateStatus::getOptions())
                            ->default(CrateStatus::PLANNED->value),
                    ])
                    ->columns(4)
                    ->collapsible(),

                Section::make('Kích thước')
                    ->description('Thông tin kích thước của thùng hàng')
                    ->schema([
                        TextInput::make('dimensions_length')
                            ->label('Chiều dài (cm)')
                            ->numeric()
                            ->minValue(0)
                            ->step(0.1)
                            ->suffix('cm')
                            ->placeholder('Nhập chiều dài'),

                        TextInput::make('dimensions_width')
                            ->label('Chiều rộng (cm)')
                            ->numeric()
                            ->minValue(0)
                            ->step(0.1)
                            ->suffix('cm')
                            ->placeholder('Nhập chiều rộng'),

                        TextInput::make('dimensions_height')
                            ->label('Chiều cao (cm)')
                            ->numeric()
                            ->minValue(0)
                            ->step(0.1)
                            ->suffix('cm')
                            ->placeholder('Nhập chiều cao'),
                    ])
                    ->columns(3)
                    ->collapsible(),


            ]);
    }
}
