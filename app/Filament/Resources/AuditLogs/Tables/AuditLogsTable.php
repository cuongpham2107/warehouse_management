<?php

namespace App\Filament\Resources\AuditLogs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Actions\DeleteAction;

class AuditLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Người dùng')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('action')
                    ->label('Hành động')
                    ->searchable(),
                TextColumn::make('table_name')
                    ->label('Tên bảng')
                    ->searchable(),
                TextColumn::make('record_id')
                    ->label('ID bản ghi')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('ip_address')
                    ->label('Địa chỉ IP')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()
                    ->icon('heroicon-o-pencil')
                    ->iconButton(),
                DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->iconButton(),
            ],position: RecordActionsPosition::BeforeColumns)
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Xóa'),
                ]),
            ]);
    }
}
