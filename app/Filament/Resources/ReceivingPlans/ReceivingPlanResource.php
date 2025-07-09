<?php

namespace App\Filament\Resources\ReceivingPlans;

use App\Filament\Resources\ReceivingPlans\Pages\CreateReceivingPlan;
use App\Filament\Resources\ReceivingPlans\Pages\EditReceivingPlan;
use App\Filament\Resources\ReceivingPlans\Pages\ListReceivingPlans;
use App\Filament\Resources\ReceivingPlans\Pages\ViewReceivingPlan;
use App\Filament\Resources\ReceivingPlans\Schemas\ReceivingPlanForm;
use App\Filament\Resources\ReceivingPlans\Schemas\ReceivingPlanInfolist;
use App\Filament\Resources\ReceivingPlans\Tables\ReceivingPlansTable;
use App\Models\ReceivingPlan;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ReceivingPlanResource extends Resource
{
    protected static ?string $model = ReceivingPlan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static string|UnitEnum|null $navigationGroup = 'Nhập kho';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'plan_code';

    public static function getNavigationLabel(): string
    {
        return 'Kế hoạch nhập kho';
    }

    public static function getModelLabel(): string
    {
        return 'Kế hoạch nhập kho';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Kế hoạch nhập kho';
    }

    public static function form(Schema $schema): Schema
    {
        return ReceivingPlanForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ReceivingPlanInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ReceivingPlansTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListReceivingPlans::route('/'),
            'create' => CreateReceivingPlan::route('/create'),
            'view' => ViewReceivingPlan::route('/{record}'),
            'edit' => EditReceivingPlan::route('/{record}/edit'),
        ];
    }
}
