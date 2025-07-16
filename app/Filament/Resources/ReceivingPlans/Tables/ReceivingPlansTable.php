<?php

namespace App\Filament\Resources\ReceivingPlans\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\DateFilter;

class ReceivingPlansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('plan_code')
                    ->label('Mã kế hoạch')
                    ->searchable(),
                TextColumn::make('vendor.vendor_name')
                    ->label('Nhà cung cấp')
                    ->sortable(),
                TextColumn::make('plan_date')
                    ->label('Ngày kế hoạch')
                    ->date()
                    ->sortable(),
                TextColumn::make('total_crates')
                    ->alignCenter(true)
                    ->label('Tổng số thùng')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_pieces')
                    ->alignCenter(true)
                    ->label('Tổng số sản phẩm')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_weight')
                    ->alignCenter(true)
                    ->label('Tổng khối lượng')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn($state) => $state->getLabel())
                    ->color(fn($state) => $state->getColor())
                    ->label('Trạng thái')
                    ->searchable(),
                TextColumn::make('creator.name')
                    ->label('Người tạo')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Ngày cập nhật')
                    ->dateTime()
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
            ->recordActions([
                ViewAction::make()
                    ->label('Xem'),
                EditAction::make()
                    ->label('Chỉnh sửa'),
                \Filament\Actions\Action::make('activate')
                    ->label('Kích hoạt')
                    ->icon('heroicon-o-bolt')
                    ->visible(fn($record) => $record->status === \App\Enums\ReceivingPlanStatus::PENDING)
                    ->requiresConfirmation()
                    ->action(function($record) {
                        $record->status = \App\Enums\ReceivingPlanStatus::IN_PROGRESS;
                        $record->save();
                    }),
                \Filament\Actions\Action::make('close')
                    ->label('Đóng kế hoạch')
                    ->icon('heroicon-o-lock-closed')
                    ->visible(fn($record) => $record->status === \App\Enums\ReceivingPlanStatus::IN_PROGRESS)
                    ->requiresConfirmation()
                    ->action(function($record) {
                        $record->status = \App\Enums\ReceivingPlanStatus::COMPLETED;
                        $record->save();
                    }),
                \Filament\Actions\Action::make('duplicate')
                    ->label('Nhân bản')
                    ->icon('heroicon-o-document-duplicate')
                    ->requiresConfirmation()
                    ->action(function($record) {
                        $new = $record->replicate(['status','plan_code','created_at','updated_at','total_crates','total_pieces','total_weight']);
                        $new->status = \App\Enums\ReceivingPlanStatus::PENDING;
                        $new->plan_code = 'RP' . now()->format('Ymd') . '-' . str_pad((string) (\App\Models\ReceivingPlan::max('id') + 1), 4, '0', STR_PAD_LEFT);
                        $new->total_crates = 0;
                        $new->total_pieces = 0;
                        $new->total_weight = 0;
                        $new->save();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Xóa'),
                ]),
            ]);
    }
}
