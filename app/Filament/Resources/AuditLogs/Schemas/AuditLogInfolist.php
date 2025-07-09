<?php

namespace App\Filament\Resources\AuditLogs\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class AuditLogInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('user.name')
                    ->numeric(),
                TextEntry::make('action'),
                TextEntry::make('table_name'),
                TextEntry::make('record_id')
                    ->numeric(),
                TextEntry::make('ip_address'),
                TextEntry::make('created_at')
                    ->dateTime(),
            ]);
    }
}
