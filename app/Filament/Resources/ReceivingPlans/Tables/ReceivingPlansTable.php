<?php

namespace App\Filament\Resources\ReceivingPlans\Tables;


use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Grouping\Group;

class ReceivingPlansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('plan_code')
                    ->label('Mã kế hoạch')
                    ->width('15%')
                    ->weight(FontWeight::Bold)
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('vendor.vendor_name')
                    ->label('Nhà cung cấp')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('plan_date')
                    ->label('Ngày lên kế hoạch')
                    ->date('H:i d/m/Y ')
                    ->alignEnd()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('arrival_date')
                    ->label('Ngày nhập kho')
                    ->date('H:i d/m/Y ')
                    ->alignEnd()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('total_crates')
                    ->badge()
                    ->width('10%')
                    ->alignCenter(true)
                    ->label('Tổng Quantity')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('total_pcs')
                    ->badge()
                    ->width('10%')
                    ->alignCenter(true)
                    ->label('Tổng PCS')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('total_weight')
                    ->badge()
                    ->width('10%')
                    ->alignCenter(true)
                    ->label('Tổng khối lượng')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('transport_garage')
                    ->label('Nhà xe vận chuyển')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('license_plate')
                    ->label('Biển số xe')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('vehicle_capacity')
                    ->label('Tải trọng xe (tấn)')
                    ->numeric()
                    ->alignCenter()
                    ->badge()
                    ->formatStateUsing(fn($state) => $state ? $state . ' tấn' : 'Chưa xác định')
                    ->toggleable(),
                TextColumn::make('status')
                    ->badge()
                    ->width('10%')
                    ->formatStateUsing(fn($state) => $state->getLabel())
                    ->color(fn($state) => $state->getColor())
                    ->label('Trạng thái')
                    ->alignCenter(true)
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('creator.name')
                    ->width('10%')
                    ->label('Người tạo')
                    ->alignCenter(true)
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Ngày cập nhật')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options(\App\Enums\ReceivingPlanStatus::getOptions()),
                SelectFilter::make('vendor_id')
                    ->label('Nhà cung cấp')
                    ->relationship('vendor', 'vendor_name'),
                // Nếu có DateFilter thì dùng, nếu không thì bỏ dòng này hoặc dùng SelectFilter với các giá trị ngày mẫu
                // DateFilter::make('plan_date')->label('Ngày kế hoạch'),
                SelectFilter::make('created_by')
                    ->label('Người tạo')
                    ->relationship('creator', 'name'),
                Filter::make('plan_code')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('value')
                            ->label('Mã kế hoạch')
                            ->placeholder('Nhập mã kế hoạch'),
                    ])
                    ->query(function ($query, $data) {
                        if (!empty($data['value'])) {
                            $query->where('plan_code', 'like', '%' . $data['value'] . '%');
                        }
                        return $query;
                    }),
            ])
            ->defaultGroup('vendor.vendor_name')
            ->groups([
                Group::make('vendor.vendor_name')
                    ->label('Nhà cung cấp')
                    ->collapsible(),
            ])
            ->defaultSort('plan_date', 'desc')
            ->recordActions([
                EditAction::make()
                    ->modal('edit_receiving_plan')
                    ->icon('heroicon-m-pencil-square')
                    ->iconButton(),
                DeleteAction::make()
                    ->icon('heroicon-m-trash')
                    ->iconButton()
                    ->action(function ($record) {
                        //Xóa các bản ghi con trong crate
                        $record->crates()->each(function ($crate) {
                            $crate->delete();
                        });
                        // Xoá bản ghi
                        $record->delete();
                    }),
                // \Filament\Actions\Action::make('activate')
                //     ->icon('heroicon-o-bolt')
                //     ->iconButton()
                //     ->visible(fn($record) => $record->status === \App\Enums\ReceivingPlanStatus::PENDING)
                //     ->requiresConfirmation()
                //     ->action(function($record) {
                //         $record->status = \App\Enums\ReceivingPlanStatus::IN_PROGRESS;
                //         $record->save();
                //     }),
                // \Filament\Actions\Action::make('close')
                //     ->iconButton()
                //     ->icon('heroicon-o-lock-closed')
                //     ->visible(fn($record) => $record->status === \App\Enums\ReceivingPlanStatus::IN_PROGRESS)
                //     ->requiresConfirmation()
                //     ->action(function($record) {
                //         $record->status = \App\Enums\ReceivingPlanStatus::COMPLETED;
                //         $record->save();
                //     }),

                ],position: RecordActionsPosition::BeforeColumns)
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Xóa')
                       
                ]),
            ])->striped()
            ->reorderableColumns();
    }
}
