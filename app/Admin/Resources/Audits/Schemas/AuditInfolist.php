<?php

namespace App\Admin\Resources\Audits\Schemas;

use App\Admin\Resources\Audits\Tables\AuditsTable;
use App\Admin\Resources\UserResource;
use App\Models\Audit;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Schemas\Schema;

class AuditInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('user_id')
                    ->url(fn (Audit $record): string => $record->user_id ? UserResource::getUrl('edit', [$record->user_id]) : '')
                    ->formatStateUsing(fn (Audit $record): string => $record->user ? $record->user->name : 'User #' . $record->user_id)
                    ->placeholder('System'),
                TextEntry::make('event')
                    ->formatStateUsing(fn (Audit $record): string => $record->event . ' - ' . class_basename($record->auditable_type) . ' (' . $record->auditable_id . ')')
                    ->url(function (Audit $record) {
                        if ($record->event != 'deleted' && isset(AuditsTable::TYPE_TO_RESOURCE[class_basename($record->auditable_type)])) {
                            return AuditsTable::TYPE_TO_RESOURCE[class_basename($record->auditable_type)]::getUrl('edit', [$record->auditable_id]);
                        }
                    }),
                TextEntry::make('user_agent'),
                TextEntry::make('ip_address'),
                TextEntry::make('url'),
                TextEntry::make('created_at')
                    ->dateTime(),
                ViewEntry::make('changes')
                    ->label('Changes')
                    ->view('admin.infolists.components.difference')
                    ->columnSpanFull(),

            ]);
    }
}
