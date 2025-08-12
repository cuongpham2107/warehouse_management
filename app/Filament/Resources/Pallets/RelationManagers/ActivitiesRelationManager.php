<?php

namespace App\Filament\Resources\Pallets\RelationManagers;

use App\Enums\PalletActivityAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ActivitiesRelationManager extends RelationManager
{
    protected static string $relationship = 'activities';

    protected static ?string $recordTitleAttribute = 'action';

    protected static ?string $title = 'Hoạt động pallet';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('action')
                    ->label('Hành động')
                    ->options(PalletActivityAction::getOptions())
                    ->default(PalletActivityAction::ATTACH_CRATE->value)
                    ->native(false)
                    ->required(),
                Textarea::make('description')
                    ->maxLength(500)
                    ->placeholder('Mô tả chi tiết hoạt động'),
                Flex::make(
                    [
                        Select::make('user_id')
                            ->relationship('user', 'name')
                            ->label('Người thực hiện')
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->required()
                            ->placeholder('ID người dùng'),
                        DateTimePicker::make('action_time')
                            ->label('Thời gian thực hiện')
                            ->default(now())
                            ->required(),
                    ]
                ),
                Flex::make(
                    [
                        TextInput::make('old_data')
                            ->label('Dữ liệu cũ')
                            ->placeholder('Dữ liệu trước khi thay đổi')
                            ->columnSpan(1),
                        TextInput::make('new_data')
                            ->label('Dữ liệu mới')
                            ->placeholder('Dữ liệu sau khi thay đổi')
                            ->columnSpan(1),
                    ]
                )
            ])->columns(1);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('action')
            ->columns([
                TextColumn::make('action')
                    ->label('Hành động')
                    ->alignCenter()
                    ->badge()
                    ->color(fn($state) => $state->getColor())
                    ->icon(fn($state) => $state->getIcon())
                    ->formatStateUsing(fn($state) => $state->getLabel())
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('description')
                    ->label('Mô tả')
                    ->searchable()
                    ->width('30%')
                    ->wrap()
                    ->limit(50)
                    ->toggleable(),
                TextColumn::make('action_time')
                    ->label('Thời gian thực hiện')                  
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('user.name')
                    ->label('Người thực hiện')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),               

                TextColumn::make('old_data')
                    ->label('Dữ liệu cũ')
                    ->searchable()      
                    ->toggleable(),
                TextColumn::make('new_data')
                    ->label('Dữ liệu mới')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Thêm hoạt động'),
                // AssociateAction::make(),
            ])
            ->recordActions([
                EditAction::make()
                    ->icon('heroicon-o-pencil')
                    ->iconButton(),
                // DissociateAction::make(),
                DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->iconButton(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
