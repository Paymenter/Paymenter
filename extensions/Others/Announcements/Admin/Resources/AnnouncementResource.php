<?php

namespace Paymenter\Extensions\Others\Announcements\Admin\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Paymenter\Extensions\Others\Announcements\Admin\Resources\AnnouncementResource\Pages\CreateAnnouncement;
use Paymenter\Extensions\Others\Announcements\Admin\Resources\AnnouncementResource\Pages\EditAnnouncement;
use Paymenter\Extensions\Others\Announcements\Admin\Resources\AnnouncementResource\Pages\ListAnnouncements;
use Paymenter\Extensions\Others\Announcements\Models\Announcement;

class AnnouncementResource extends Resource
{
    protected static ?string $model = Announcement::class;

    protected static string|\BackedEnum|null $navigationIcon = 'ri-megaphone-line';

    protected static string|\BackedEnum|null $activeNavigationIcon = 'ri-megaphone-fill';

    protected static string|\UnitEnum|null $navigationGroup = 'Administration';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
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
                TextInput::make('slug')
                    ->label('Slug')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Enter the slug of the announcement'),
                TextInput::make('description')
                    ->label('Description')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Short description to show on the announcement list'),
                DateTimePicker::make('published_at')
                    ->label('Published At')
                    ->required()
                    ->placeholder('Enter the date and time when the announcement should be published'),
                Toggle::make('is_active')
                    ->label('Is Published')
                    ->default(false),
                RichEditor::make('content')
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
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('published_at')
                    ->searchable()
                    ->dateTime()
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('Published')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAnnouncements::route('/'),
            'create' => CreateAnnouncement::route('/create'),
            'edit' => EditAnnouncement::route('/{record}/edit'),
        ];
    }
}
