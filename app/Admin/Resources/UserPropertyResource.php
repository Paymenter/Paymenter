<?php

namespace App\Admin\Resources;

use App\Admin\Resources\UserPropertyResource\Pages;
use App\Admin\Resources\UserPropertyResource\RelationManagers;
use App\Models\UserProperty;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserPropertyResource extends Resource
{
    protected static ?string $model = UserProperty::class;

    protected static ?string $navigationGroup = 'Configuration';

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('type')->options([
                    'text' => 'Text',
                    'number' => 'Number',
                    'select' => 'Select',
                    'checkbox' => 'Checkbox',
                    'radio' => 'Radio',
                    'date' => 'Date'
                ])->required(),
                Forms\Components\Textarea::make('description')->nullable()->columnSpanFull()->rows(2),
                Forms\Components\TextInput::make('key')->required(),
                Forms\Components\TextInput::make('validation')->nullable(),
                Forms\Components\TagsInput::make('allowed_values')->nullable()->columnSpanFull(),

                Forms\Components\Section::make()
                    ->columns([
                        'sm' => 1,
                        'md' => 3,
                    ])
                    ->schema([
                        Forms\Components\Toggle::make('admin_only')->default(false),
                        Forms\Components\Toggle::make('required')->default(false),
                        Forms\Components\Toggle::make('show_on_invoice')->default(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Property')
                    ->description(fn (UserProperty $record): string => mb_strimwidth(($record->description ?? ''),
                        0,
                        75,
                        "..."
                    )),
                Tables\Columns\TextColumn::make('key'),
                Tables\Columns\TextColumn::make('type')->formatStateUsing(fn ($state) => str($state)->title()),
                Tables\Columns\ToggleColumn::make('admin_only'),
                Tables\Columns\ToggleColumn::make('required'),
                Tables\Columns\ToggleColumn::make('show_on_invoice'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListUserProperty::route('/'),
            'create' => Pages\CreateUserProperty::route('/create'),
            'edit' => Pages\EditUserProperty::route('/{record}/edit'),
        ];
    }
}
