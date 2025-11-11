<?php

namespace App\Admin\Resources;

use App\Admin\Resources\Common\RelationManagers\PropertiesRelationManager;
use App\Admin\Resources\UserResource\Pages\CreateUser;
use App\Admin\Resources\UserResource\Pages\EditUser;
use App\Admin\Resources\UserResource\Pages\ListUsers;
use App\Admin\Resources\UserResource\Pages\ShowBillingAgreements;
use App\Admin\Resources\UserResource\Pages\ShowCredits;
use App\Admin\Resources\UserResource\Pages\ShowInvoices;
use App\Admin\Resources\UserResource\Pages\ShowServices;
use App\Admin\Resources\UserResource\Pages\ShowTickets;
use App\Models\Credit;
use App\Models\User;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Administration';

    protected static string|\BackedEnum|null $navigationIcon = 'ri-group-line';

    protected static string|\BackedEnum|null $activeNavigationIcon = 'ri-group-fill';

    public static function getGloballySearchableAttributes(): array
    {
        return ['first_name', 'last_name', 'email'];
    }

    public static function getGlobalSearchResultTitle(Model $record): string|Htmlable
    {
        return $record->name;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('first_name')->translateLabel()->required(),
                TextInput::make('last_name')->translateLabel()->required(),
                TextInput::make('email')->translateLabel()->email()->required()->unique('users', 'email', ignoreRecord: true),

                TextInput::make('password')->translateLabel()->password()->revealable()
                    ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->required(fn (string $operation): bool => $operation === 'create'),
                Select::make('role_id')->translateLabel()->relationship('role', 'name')->searchable()->preload(),
                Toggle::make('tfa_secret')
                    ->label('Two Factor Authentication')
                    ->disabled(fn ($record) => !$record?->tfa_secret)
                    ->dehydrateStateUsing(fn ($state, $record) => $state ? $record->tfa_secret : null)
                    ->formatStateUsing(fn ($record) => $record && $record->tfa_secret ? true : false)
                    ->hiddenOn(['create']),

                Toggle::make('email_verified_at')
                    ->label('Email Verified')
                    ->dehydrateStateUsing(function ($state, $record) {
                        if ($state && !$record->email_verified_at) {
                            return now();
                        }

                        return $state ? $record->email_verified_at : null;
                    })
                    ->hiddenOn(['create']),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('first_name')
                    ->searchable()
                    ->sortable()
                    ->description(function (User $user) {
                        if (count($user->credits) <= 0) {
                            return null;
                        }

                        return 'Earnings - ' . implode(', ', $user->credits->map(function (Credit $credit) {
                            return "$credit->currency_code: $credit->amount";
                        })->toArray());
                    }),
                TextColumn::make('last_name')->searchable()->sortable(),
                TextColumn::make('email')->searchable()->sortable(),
                TextColumn::make('role.name')->sortable(),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->relationship('role', 'name')
                    ->searchable()
                    ->preload(),
                Filter::make('email_verified')
                    ->label('Email Verified'),
                Filter::make('has_active_services')
                    ->label('Has Active Services')
                    ->query(fn ($query) => $query->whereHas('services', function ($q) {
                        $q->where('status', 'active');
                    })),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            PropertiesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
            'services' => ShowServices::route('/{record}/services'),
            'invoices' => ShowInvoices::route('/{record}/invoices'),
            'credits' => ShowCredits::route('/{record}/credits'),
            'tickets' => ShowTickets::route('/{record}/tickets'),
            'billing-agreements' => ShowBillingAgreements::route('/{record}/billing-agreements'),
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            EditUser::class,
            ShowServices::class,
            ShowInvoices::class,
            ShowCredits::class,
            ShowTickets::class,
            ShowBillingAgreements::class,
        ]);
    }
}
