<?php

namespace App\Admin\Resources\Audits;

use App\Admin\Resources\Audits\Pages\ListAudits;
use App\Admin\Resources\Audits\Pages\ViewAudit;
use App\Admin\Resources\Audits\Schemas\AuditInfolist;
use App\Admin\Resources\Audits\Tables\AuditsTable;
use App\Models\Audit;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class AuditResource extends Resource
{
    protected static ?string $model = Audit::class;

    protected static string|BackedEnum|null $navigationIcon = 'ri-file-copy-2-line';

    protected static string|BackedEnum|null $activeNavigationIcon = 'ri-file-copy-2-fill';

    protected static string|\UnitEnum|null $navigationGroup = 'System';

    public static function infolist(Schema $schema): Schema
    {
        return AuditInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AuditsTable::configure($table);
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
            'index' => ListAudits::route('/'),
            'view' => ViewAudit::route('/{record}'),
        ];
    }
}
