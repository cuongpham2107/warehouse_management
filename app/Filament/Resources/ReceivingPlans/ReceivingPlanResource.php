<?php

namespace App\Filament\Resources\ReceivingPlans;

use App\Filament\Resources\ReceivingPlans\Pages\ListReceivingPlans;
use App\Filament\Resources\ReceivingPlans\Pages\EditReceivingPlan;
use App\Filament\Resources\ReceivingPlans\RelationManagers\CratesRelationManager;
use App\Filament\Resources\ReceivingPlans\Schemas\ReceivingPlanForm;
use App\Filament\Resources\ReceivingPlans\Tables\ReceivingPlansTable;
use App\Models\ReceivingPlan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ReceivingPlanResource extends Resource
{
    protected static ?string $model = ReceivingPlan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

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

    // public static function getNavigationBadge(): ?string
    // {
    //     return static::getModel()::where('status', 'active')->count();
    // }

    public static function getPluralModelLabel(): string
    {
        return 'Kế hoạch nhập kho';
    }

    public static function form(Schema $schema): Schema
    {
        return ReceivingPlanForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ReceivingPlansTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            CratesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListReceivingPlans::route('/'),
            // 'create' => CreateReceivingPlan::route('/create'),
            'edit' => EditReceivingPlan::route('/{record}/edit'),
        ];
    }
}
