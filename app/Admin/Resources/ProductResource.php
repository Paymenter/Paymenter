<?php

namespace App\Admin\Resources;

use App\Admin\Resources\ProductResource\Pages;
use App\Admin\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Filament\Forms\Components\Tabs;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Tabs')
                    ->tabs([
                        Tabs\Tab::make('General')
                            ->columns(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                                        if (($get('slug') ?? '') !== Str::slug($old)) {
                                            return;
                                        }

                                        $set('slug', Str::slug($state));
                                    }),
                                Forms\Components\TextInput::make('slug')->required(),
                                Forms\Components\TextInput::make('stock')->integer()->nullable(),
                                Forms\Components\TextInput::make('per_user_limit')->integer()->nullable(),
                                Forms\Components\Textarea::make('description')->nullable(),
                                Forms\Components\FileUpload::make('image_url')->label('Image')->nullable()->acceptedFileTypes(['image/*']),
                                Forms\Components\Select::make('category_id')
                                    ->relationship('category', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('name')
                                            ->required()
                                            ->maxLength(255)
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                                                if (($get('slug') ?? '') !== Str::slug($old)) {
                                                    return;
                                                }

                                                $set('slug', Str::slug($state));
                                            }),
                                        Forms\Components\TextInput::make('slug'),
                                        Forms\Components\Textarea::make('description')
                                            ->required(),

                                    ])
                                    ->required(),
                            ]),
                        Tabs\Tab::make('Pricing')
                            ->schema([
                                Forms\Components\Repeater::make('prices')
                                    ->label('')
                                    ->addActionLabel('Add New Price')
                                    ->relationship('prices')
                                    ->name('name')
                                    ->reorderable()
                                    ->collapsible()
                                    ->collapsed()
                                    ->orderColumn()
                                    ->defaultItems(0)
                                    ->columns(2)
                                    ->itemLabel(fn (array $state) => $state['name'])
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->required()
                                            ->columnSpanFull()
                                            ->live(onBlur: true)
                                            ->maxLength(255),
                                        Forms\Components\Select::make('type')
                                            ->options([
                                                'free' => 'Free',
                                                'one-time' => 'One Time',
                                                'recurring' => 'Recurring',
                                            ])
                                            ->required()
                                            ->live(debounce: 300)
                                            ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                                                if ($state === 'free') {
                                                    $set('every', null);
                                                    $set('price', 0);
                                                }
                                            })
                                            ->placeholder('Select the type of the price')
                                            ->default('free'),
                                        Forms\Components\TextInput::make('price')
                                            ->required()
                                            ->numeric(),
                                        
                                            // This is stored in 
                                        Forms\Components\TextInput::make('billing_period')
                                            ->required()
                                            ->label('Time Interval')
                                            ->default(1)
                                            ->hidden(fn (Get $get) => $get('type') !== 'recurring'),
                                        // Hourly, Daily, Weekly, Monthly, Yearly
                                        Forms\Components\Select::make('billing_unit')
                                            ->options([
                                                'hour' => 'Hour',
                                                'day' => 'Day',
                                                'week' => 'Week',
                                                'month' => 'Month',
                                                'year' => 'Year',
                                            ])
                                            ->label('Billing period')
                                            ->required()
                                            ->hidden(fn (Get $get) => $get('type') !== 'recurring'),
                                    ]),
                            ]),
                    ]),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('slug'),
                Tables\Columns\TextColumn::make('description'),
                Tables\Columns\TextColumn::make('category.name')->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
