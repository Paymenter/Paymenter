<?php

namespace App\Admin\Resources;

use App\Admin\Resources\Common\RelationManagers\PropertiesRelationManager;
use App\Admin\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationGroup = 'Administration';

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function getGloballySearchableAttributes(): array
    {
        return ['first_name', 'last_name', 'email'];
    }

    public static function getGlobalSearchResultTitle(Model $record): string|Htmlable
    {
        return $record->name;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('first_name')->translateLabel()->required(),
                TextInput::make('last_name')->translateLabel()->required(),
                TextInput::make('email')->translateLabel()->email()->required()->unique('users', 'email', ignoreRecord: true),

                TextInput::make('password')->translateLabel()->password()->revealable()
                    ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->required(fn (string $operation): bool => $operation === 'create'),
                TextInput::make('credits')->translateLabel()->numeric()->default(0),

                Select::make('role_id')->translateLabel()->relationship('role', 'name')->searchable()->preload(),
                Toggle::make('tfa_secret')
                    ->label('Two Factor Authentication')
                    ->disabled(fn ($record) => !$record->tfa_secret)
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
                Tables\Columns\TextColumn::make('first_name')->searchable(),
                Tables\Columns\TextColumn::make('last_name'),
                Tables\Columns\TextColumn::make('email'),
                Tables\Columns\TextColumn::make('role.name'),
                Tables\Columns\TextColumn::make('credits'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->relationship('role', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
            'services' => Pages\ShowServices::route('/{record}/services'),
            'invoices' => Pages\ShowInvoices::route('/{record}/invoices'),
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {

        return $page->generateNavigationItems([
            Pages\EditUser::class,
            Pages\ShowServices::class,
            Pages\ShowInvoices::class,
        ]);
    }
}
