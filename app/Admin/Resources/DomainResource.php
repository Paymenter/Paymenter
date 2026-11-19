<?php

namespace App\Admin\Resources;

use App\Admin\Clusters\Services;
use App\Admin\Resources\DomainResource\Pages\EditDomain;
use App\Admin\Resources\DomainResource\Pages\ListDomains;
use App\Enums\DomainStatus;
use App\Models\Domain;
use App\Services\Domain\DomainService;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Throwable;

class DomainResource extends Resource
{
    protected static ?string $model = Domain::class;

    protected static ?string $cluster = Services::class;

    protected static string|\BackedEnum|null $navigationIcon = 'ri-global-line';

    protected static string|\BackedEnum|null $activeNavigationIcon = 'ri-global-fill';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->disabled(),
                Select::make('status')
                    ->options(array_combine(
                        array_column(DomainStatus::cases(), 'value'),
                        array_map(fn (DomainStatus $status) => ucfirst($status->value), DomainStatus::cases())
                    ))
                    ->required(),
                DatePicker::make('expires_at'),
                Toggle::make('auto_renew'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('service.user.email')->label('Client')->searchable(),
                TextColumn::make('status')->badge(),
                TextColumn::make('expires_at')->date()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->options(array_combine(
                    array_column(DomainStatus::cases(), 'value'),
                    array_map(fn (DomainStatus $status) => ucfirst($status->value), DomainStatus::cases())
                )),
            ])
            ->recordActions([
                ViewAction::make()->authorize('view'),
                Action::make('sync')
                    ->icon('ri-refresh-line')
                    ->authorize('update')
                    ->action(function (Domain $record) {
                        $registrar = DomainService::registrarForDomain($record);

                        if (!$registrar) {
                            Notification::make()->title('No registrar available')->danger()->send();

                            return;
                        }

                        try {
                            DomainService::apply($record, $registrar->getInfo($record), 'admin:sync');
                            Notification::make()->title('Domain synced')->success()->send();
                        } catch (Throwable $e) {
                            $record->update(['sync_error' => $e->getMessage(), 'last_synced_at' => now()]);
                            Notification::make()->title('Sync failed')->body($e->getMessage())->danger()->send();
                        }
                    }),
                EditAction::make()->authorize('update'),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('service.user.email')->label('Client')->placeholder('-'),
                TextEntry::make('status')->badge(),
                TextEntry::make('server.name')->label('Registrar')->placeholder('-'),
                TextEntry::make('registered_at')->date()->placeholder('-'),
                TextEntry::make('expires_at')->date()->placeholder('-'),
                TextEntry::make('grace_until')->date()->placeholder('-'),
                TextEntry::make('redemption_until')->date()->placeholder('-'),
                IconEntry::make('auto_renew')->boolean(),
                IconEntry::make('locked')->boolean(),
                IconEntry::make('privacy')->boolean(),
                TextEntry::make('nameservers')
                    ->placeholder('-')
                    ->formatStateUsing(fn ($state) => is_array($state) ? implode(', ', $state) : $state),
                TextEntry::make('last_synced_at')->dateTime()->since()->placeholder('Never'),
                TextEntry::make('last_transition_reason')->placeholder('-')->columnSpanFull(),
                TextEntry::make('sync_error')
                    ->placeholder('-')
                    ->columnSpanFull()
                    ->color('danger'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDomains::route('/'),
            'edit' => EditDomain::route('/{record}/edit'),
        ];
    }
}
