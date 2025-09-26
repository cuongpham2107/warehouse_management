<?php

namespace App\Filament\Resources\Crates\Tables;

use App\Enums\CrateStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Actions\Action;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use App\Models\WarehouseLocation;
use App\Enums\PalletActivityAction;

class CratesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->columns([
                TextColumn::make('crate_id')
                    ->label('Mã kiện hàng')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                TextColumn::make('receivingPlan.plan_code')
                    ->label('Kế hoạch nhập kho')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('pieces')
                    ->label('Quantity')
                    ->alignCenter(true)
                    ->numeric()
                    ->sortable(),
                TextColumn::make('pcs')
                    ->label('PCS')
                    ->searchable(),
                TextColumn::make('type')
                    ->label('Loại đóng gói')
                    ->badge()
                    ->searchable(),

                TextColumn::make('gross_weight')
                    ->label('Trọng lượng')
                    ->alignCenter(true)
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn($state) => number_format($state, 2) . ' kg'),

                TextColumn::make('dimensions')
                    ->label('Kích thước (L×W×H)')
                    ->getStateUsing(function ($record) {
                        $l = $record->dimensions_length ?? 0;
                        $w = $record->dimensions_width ?? 0;
                        $h = $record->dimensions_height ?? 0;
                        return "{$l} × {$w} × {$h} cm";
                    })
                    ->toggleable(),

                TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->color(fn($state): string => $state instanceof CrateStatus ? $state->getColor() : 'gray')
                    ->formatStateUsing(fn($state): string => $state instanceof CrateStatus ? $state->getLabel() : ($state ?? 'N/A'))
                    ->icon(fn($state): string => $state instanceof CrateStatus ? $state->getIcon() : 'heroicon-m-question-mark-circle')
                    ->sortable(),

                TextColumn::make('barcode')
                    ->label('Mã vạch')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->copyable(),

                TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Ngày cập nhật')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),


            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options(CrateStatus::class)
                    ->multiple()
                    ->preload(),
                Filter::make('list_crates')
                    ->schema([
                        Textarea::make('crate_ids')
                            ->label('Danh sách mã thùng hàng')
                            ->helperText('Nhập mã thùng hàng, mỗi mã trên một dòng')
                            ->placeholder('Nhập mã thùng hàng để lọc')
                            ->rows(5)
                            ->columnSpanFull(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (empty($data['crate_ids'])) {
                            return $query;
                        }
                        return $query->whereIn('crate_id', explode("\n", trim($data['crate_ids'] ?? '')));
                    })


            ])
            ->defaultGroup('receivingPlan.plan_code')
            ->groups([
                Group::make('receivingPlan.plan_code')
                    ->label('Kế hoạch nhập kho')
                    ->collapsible(),
            ])
            ->recordActions([
                Action::make('create_pallet')
                    ->label('Tạo pallet từ kiện hàng')
                    ->icon('heroicon-o-plus')
                    ->iconButton()
                    ->schema([
                        Section::make('Thông tin pallet')
                            ->columns(2)
                            ->schema([
                                TextInput::make('pallet_id')
                                    ->label('Mã pallet')
                                    ->required()
                                    ->placeholder('Nhập mã pallet'),
                                TextInput::make('location_code')
                                    ->required()
                                    ->label('Mã vị trí')
                                    ->placeholder('Nhập mã vị trí (nếu có)')
                                    ->datalist(
                                        fn() => WarehouseLocation::query()->pluck('location_code')->all()
                                    ),
                            ]),

                    ])
                    ->action(function (array $data, $record) {
                        $pallet = $record->pallet()->create([
                            'pallet_id' => $data['pallet_id'],
                            'location_code' => $data['location_code'] ?? null,
                            'status' => \App\Enums\PalletStatus::STORED,
                        ]);

                        $pallet->activities()->create([
                            'user_id' => auth()->id(),
                            'action' => PalletActivityAction::ATTACH_CRATE->value,
                            'description' => 'Tạo pallet từ kiện hàng',
                            'action_time' => now(),
                        ]);
                        $record->status = CrateStatus::STORED;
                        $record->save();
                        $record->refresh();
                    }),
                
                EditAction::make()
                    ->icon('heroicon-o-pencil')
                    ->iconButton(),
                DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->iconButton(),
            ], position: RecordActionsPosition::BeforeColumns)
            ->headerActions([

            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Xóa đã chọn'),
                ])->label('Hành động hàng loạt'),
            ])
            ->defaultSort('created_at', 'desc')
            ->reorderableColumns();
    }
}
