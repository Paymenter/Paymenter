<?php

namespace App\Admin\Resources;

use App\Admin\Resources\CategoryResource\Pages\CreateCategory;
use App\Admin\Resources\CategoryResource\Pages\EditCategory;
use App\Admin\Resources\CategoryResource\Pages\ListCategories;
use App\Admin\Resources\CategoryResource\RelationManagers\ProductsRelationManager;
use App\Models\Category;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Administration';

    protected static string|\BackedEnum|null $navigationIcon = 'ri-folder-6-line';

    protected static string|\BackedEnum|null $activeNavigationIcon = 'ri-folder-6-fill';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                        if (($get('slug') ?? '') !== Str::slug($old)) {
                            return;
                        }

                        $set('slug', Str::slug($state));
                    }),
                TextInput::make('slug')
                    ->required(),
                RichEditor::make('description')
                    ->required(),
                Select::make('parent_id')
                    ->relationship('parent', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Parent Category')
                    // Disallow having same category as it's own parent
                    ->disableOptionWhen(fn (string $value, ?Category $record): bool => $record && (int) $value === $record->id),
                FileUpload::make('image')
                    ->label('Image')
                    ->nullable()
                    ->visibility('public')
                    ->disk('public')
                    ->acceptedFileTypes(['image/*'])
                    ->columnSpanFull(),
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')
                    ->searchable()
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
                    DeleteBulkAction::make()->before(function (DeleteBulkAction $action, $records) {
                        foreach ($records as $record) {
                            if ($record->products()->exists() || $record->children()->exists()) {
                                Notification::make()
                                    ->title('Cannot delete category')
                                    ->body('The category has products or children categories.')
                                    ->duration(5000)
                                    ->icon('ri-error-warning-line')
                                    ->danger()
                                    ->send();
                                $action->cancel();
                            }
                        }
                    }),
                ]),
            ])
            ->defaultSort(function (Builder $query): Builder {
                return $query
                    ->orderBy('sort', 'asc');
            })
            ->reorderable('sort');
    }

    public static function getRelations(): array
    {
        return [
            ProductsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCategories::route('/'),
            'create' => CreateCategory::route('/create'),
            'edit' => EditCategory::route('/{record}/edit'),
        ];
    }
}
