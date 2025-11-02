<?php

namespace Paymenter\Extensions\Others\Knowledgebase\Admin\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Paymenter\Extensions\Others\Knowledgebase\Admin\Resources\KnowledgeArticleResource\Pages\CreateKnowledgeArticle;
use Paymenter\Extensions\Others\Knowledgebase\Admin\Resources\KnowledgeArticleResource\Pages\EditKnowledgeArticle;
use Paymenter\Extensions\Others\Knowledgebase\Admin\Resources\KnowledgeArticleResource\Pages\ListKnowledgeArticles;
use Paymenter\Extensions\Others\Knowledgebase\Models\KnowledgeArticle;
use Paymenter\Extensions\Others\Knowledgebase\Models\KnowledgeCategory;

class KnowledgeArticleResource extends Resource
{
    protected static ?string $model = KnowledgeArticle::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $slug = 'knowledgebase/articles';

    protected static string|\BackedEnum|null $navigationIcon = 'ri-file-text-line';

    protected static string|\BackedEnum|null $activeNavigationIcon = 'ri-file-text-fill';

    protected static string|\UnitEnum|null $navigationGroup = 'Knowledgebase';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category_id')
                    ->label('Category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->default(fn () => request()->integer('category'))
                    ->required(fn () => !request()->filled('category'))
                    ->hidden(fn () => request()->filled('category')),
                TextInput::make('title')
                    ->label('Title')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                        if (($get('slug') ?? '') !== Str::slug($old)) {
                            return;
                        }

                        $set('slug', Str::slug((string) $state));
                    })
                    ->placeholder('Article title'),
                TextInput::make('slug')
                    ->label('Slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(table: 'ext_kb_articles', column: 'slug', ignoreRecord: true)
                    ->placeholder('Unique slug for the article'),
                TextInput::make('summary')
                    ->label('Summary')
                    ->maxLength(500)
                    ->placeholder('Short description displayed in lists'),
                Select::make('is_active')
                    ->label('Visible to clients')
                    ->options([
                        true => 'Yes',
                        false => 'No',
                    ])
                    ->default(true)
                    ->required(),
                DateTimePicker::make('published_at')
                    ->label('Publish At')
                    ->seconds(false)
                    ->default(now()),
                TextInput::make('view_count')
                    ->label('View Count')
                    ->numeric()
                    ->minValue(0)
                    ->default(0),
                RichEditor::make('content')
                    ->label('Content')
                    ->columnSpanFull()
                    ->required()
                    ->placeholder('Article content'),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->columns([
                TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('category.name')
                    ->label('Category')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('published_at')
                    ->label('Published At')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime()
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('view_count')
                    ->label('Views')
                    ->sortable()
                    ->badge()
                    ->formatStateUsing(fn (?int $state) => $state ?? 0),
            ])
            ->defaultSort('sort_order')
            ->filters([
                SelectFilter::make('category_id')
                    ->label('Category')
                    ->options(fn () => KnowledgeCategory::query()->pluck('name', 'id')->all()),
                SelectFilter::make('is_active')
                    ->label('Active Status')
                    ->options([
                        '1' => 'Active',
                        '0' => 'Inactive',
                    ]),
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('category');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListKnowledgeArticles::route('/'),
            'create' => CreateKnowledgeArticle::route('/create'),
            'edit' => EditKnowledgeArticle::route('/{record}/edit'),
        ];
    }
}
