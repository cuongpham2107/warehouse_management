<?php

namespace App\Filament\Resources\AuditLogs;

use App\Filament\Resources\AuditLogs\Pages\CreateAuditLog;
use App\Filament\Resources\AuditLogs\Pages\EditAuditLog;
use App\Filament\Resources\AuditLogs\Pages\ListAuditLogs;
use App\Filament\Resources\AuditLogs\Pages\ViewAuditLog;
use App\Filament\Resources\AuditLogs\Schemas\AuditLogForm;
use App\Filament\Resources\AuditLogs\Schemas\AuditLogInfolist;
use App\Filament\Resources\AuditLogs\Tables\AuditLogsTable;
use App\Models\AuditLog;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AuditLogResource extends Resource
{
    protected static ?string $model = AuditLog::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static string|UnitEnum|null $navigationGroup = '📊 Báo cáo & Theo dõi';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'table_name';

    public static function getNavigationLabel(): string
    {
        return 'Nhật ký hệ thống';
    }

    public static function getModelLabel(): string
    {
        return 'Nhật ký hệ thống';
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereDate('created_at', today())->count();
    }

    public static function getPluralModelLabel(): string
    {
        return 'Audit Log';
    }

    public static function form(Schema $schema): Schema
    {
        return AuditLogForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AuditLogInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AuditLogsTable::configure($table);
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
            'index' => ListAuditLogs::route('/'),
            'create' => CreateAuditLog::route('/create'),
            'view' => ViewAuditLog::route('/{record}'),
            'edit' => EditAuditLog::route('/{record}/edit'),
        ];
    }
}
