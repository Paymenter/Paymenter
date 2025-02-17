<?php

namespace Paymenter\Extensions\Others\Announcements\Admin\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Paymenter\Extensions\Others\Announcements\Admin\Resources\AnnouncementResource\Pages;
use Paymenter\Extensions\Others\Announcements\Models\Announcement;

class AnnouncementResource extends Resource
{
    protected static ?string $model = Announcement::class;

    protected static ?string $navigationIcon = 'ri-megaphone-line';

    protected static ?string $activeNavigationIcon = 'ri-megaphone-fill';

    protected static ?string $navigationGroup = 'Administration';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Title')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                        if (($get('slug') ?? '') !== Str::slug($old)) {
                            return;
                        }

                        $set('slug', Str::slug($state));
                    })
                    ->placeholder('Enter the title of the announcement'),
                Forms\Components\TextInput::make('slug')
                    ->label('Slug')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Enter the slug of the announcement'),
                Forms\Components\TextInput::make('description')
                    ->label('Description')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Short description to show on the announcement list'),
                Forms\Components\DateTimePicker::make('published_at')
                    ->label('Published At')
                    ->required()
                    ->placeholder('Enter the date and time when the announcement should be published'),
                Forms\Components\Toggle::make('is_active')
                    ->label('Is Published')
                    ->default(false),
                Forms\Components\RichEditor::make('content')
                    ->columnSpanFull()
                    ->label('Content')
                    ->required()
                    ->placeholder('Enter the content of the announcement'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('published_at')
                    ->searchable()
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Published')
                    ->boolean()
                    ->sortable(),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAnnouncements::route('/'),
            'create' => Pages\CreateAnnouncement::route('/create'),
            'edit' => Pages\EditAnnouncement::route('/{record}/edit'),
        ];
    }
}
