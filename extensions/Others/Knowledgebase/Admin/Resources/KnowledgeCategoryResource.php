<?php

namespace Paymenter\Extensions\Others\Knowledgebase\Admin\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Schema as SchemaFacade;
use Illuminate\Support\Str;
use Filament\Notifications\Notification;
use Paymenter\Extensions\Others\Knowledgebase\Admin\Resources\KnowledgeCategoryResource\Pages\CreateKnowledgeCategory;
use Paymenter\Extensions\Others\Knowledgebase\Admin\Resources\KnowledgeCategoryResource\Pages\EditKnowledgeCategory;
use Paymenter\Extensions\Others\Knowledgebase\Admin\Resources\KnowledgeCategoryResource\Pages\ListKnowledgeCategories;
use Paymenter\Extensions\Others\Knowledgebase\Admin\Resources\KnowledgeCategoryResource\RelationManagers\ArticlesRelationManager;
use Paymenter\Extensions\Others\Knowledgebase\Models\KnowledgeCategory;

class KnowledgeCategoryResource extends Resource
{
    protected static ?string $model = KnowledgeCategory::class;

    protected static string|\BackedEnum|null $navigationIcon = 'ri-book-2-line';

    protected static string|\BackedEnum|null $activeNavigationIcon = 'ri-book-2-fill';

    protected static ?string $navigationLabel = 'Knowledgebase';

    protected static string|\UnitEnum|null $navigationGroup = 'Administration';

    protected static ?int $navigationSort = 30;

    public static function form(Schema $schema): Schema
    {
        if (! SchemaFacade::hasTable('ext_kb_categories')) {
            return $schema->components([]);
        }

        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                        if (($get('slug') ?? '') !== Str::slug($old)) {
                            return;
                        }

                        $set('slug', Str::slug((string) $state));
                    })
                    ->placeholder('Knowledgebase category name'),
                TextInput::make('slug')
                    ->label('Slug')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Unique slug for the category'),
                RichEditor::make('description')
                    ->label('Description')
                    ->placeholder('Optional description displayed to users')
                    ->columnSpanFull(),
                TextInput::make('sort_order')
                    ->label('Sort Order')
                    ->numeric()
                    ->default(0)
                    ->columnSpan(1),
                Select::make('is_active')
                    ->label('Visible to clients')
                    ->options([
                        true => 'Yes',
                        false => 'No',
                    ])
                    ->default(true)
                    ->required()
                    ->columnSpan(1),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        if (! SchemaFacade::hasTable('ext_kb_categories')) {
            return $table->columns([]);
        }

        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                TextColumn::make('sort_order')
                    ->label('Order')
                    ->sortable(),
                TextColumn::make('articles_count')
                    ->label('Articles')
                    ->counts('articles')
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->tooltip('Remove all articles from the selected categories before deleting them.')
                        ->disabled(fn($records) => $records->contains(fn($record) => $record->articles()->exists()))
                        ->before(function (DeleteBulkAction $action, $records) {
                            $blocked = collect($records)->filter(fn($record) => $record->articles()->exists());

                            if ($blocked->isEmpty()) {
                                return;
                            }

                            Notification::make()
                                ->title('Cannot delete category')
                                ->body('Remove all articles from the category before deleting it.')
                                ->danger()
                                ->send();

                            $action->cancel();
                        }),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ArticlesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListKnowledgeCategories::route('/'),
            'create' => CreateKnowledgeCategory::route('/create'),
            'edit' => EditKnowledgeCategory::route('/{record}/edit'),
        ];
    }
}
