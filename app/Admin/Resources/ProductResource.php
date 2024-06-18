<?php

namespace App\Admin\Resources;

use App\Admin\Resources\ProductResource\Pages;
use App\Admin\Resources\ProductResource\RelationManagers;
use App\Classes\FilamentInput;
use App\Helpers\ExtensionHelper;
use App\Models\Currency;
use App\Models\Product;
use App\Models\Server;
use Filament\Forms;
use Filament\Forms\Components\Grid;
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
use Filament\Infolists;
use Illuminate\Support\Facades\Cache;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Tabs')
                    ->persistTabInQueryString()
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
                                Forms\Components\RichEditor::make('description')->nullable()->columnSpanFull(),
                                Forms\Components\FileUpload::make('image')->label('Image')->nullable()->acceptedFileTypes(['image/*']),
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
                                        Forms\Components\Select::make('parent_id')
                                            ->relationship('categories', 'name')
                                            ->searchable()
                                            ->preload(),
                                    ])
                                    ->required(),
                            ]),
                        Tabs\Tab::make('Pricing')
                            ->schema([
                                Forms\Components\Repeater::make('plan')
                                    ->addActionLabel('Add new plan')
                                    ->relationship('plans')
                                    ->name('name')
                                    ->reorderable()
                                    ->cloneable()
                                    ->collapsible()
                                    ->collapsed()
                                    ->orderColumn()
                                    ->defaultItems(0)
                                    ->columns(2)
                                    ->itemLabel(fn (array $state) => $state['name'])
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->required()
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

                                        Forms\Components\TextInput::make('billing_period')
                                            ->required()
                                            ->label('Time Interval')
                                            ->default(1)
                                            ->hidden(fn (Get $get) => $get('type') !== 'recurring'),

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
                                            ->default('month')
                                            ->hidden(fn (Get $get) => $get('type') !== 'recurring'),
                                        Forms\Components\Repeater::make('pricing')
                                            ->hidden(fn (Get $get) => $get('type') === 'free')
                                            ->columns(3)
                                            ->addActionLabel('Add new price')
                                            ->reorderable(false)
                                            ->relationship('prices')
                                            ->columnSpanFull()
                                            ->maxItems(Currency::count())
                                            ->defaultItems(1)
                                            ->itemLabel(fn (array $state) => $state['currency_code'])
                                            ->schema([
                                                Forms\Components\Select::make('currency_code')
                                                    ->options(function (Get $get, ?string $state) {
                                                        $pricing = collect($get('../../pricing'))->pluck('currency_code');
                                                        if ($state !== null) {
                                                            $pricing = $pricing->filter(function ($code) use ($state) {
                                                                return $code !== $state;
                                                            });
                                                        }
                                                        $pricing = $pricing->filter(function ($code) use ($state) {
                                                            return $code !== null;
                                                        });

                                                        return Currency::whereNotIn('code', $pricing)->pluck('code', 'code');
                                                    })
                                                    ->live()
                                                    ->default(config('settings.default_currency'))
                                                    ->required(),
                                                Forms\Components\TextInput::make('price')
                                                    ->required()
                                                    ->label('Price')
                                                    // Suffix based on chosen currency
                                                    ->prefix(fn (Get $get) => Currency::where('code', $get('currency_code'))->first()?->prefix)
                                                    ->suffix(fn (Get $get) => Currency::where('code', $get('currency_code'))->first()?->suffix)
                                                    ->live(onBlur: true)
                                                    ->hidden(fn (Get $get) => $get('type') === 'free'),
                                                Forms\Components\TextInput::make('setup_fee')
                                                    ->label('Setup fee')
                                                    ->live(onBlur: true)
                                                    ->hidden(fn (Get $get) => $get('type') === 'free'),
                                            ]),
                                    ]),
                            ]),

                        Tabs\Tab::make('Server')
                            ->schema([
                                Forms\Components\Select::make('server_id')
                                    ->relationship('server', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->live(),

                                Grid::make()
                                    ->hidden(fn (Get $get) => $get('server_id') === null)
                                    ->schema(
                                        function (Get $get) {
                                            $server = $get('server_id');
                                            \Debugbar::info('test');
                                            if ($server == null) {
                                                return [];
                                            }
                                            $settings = [];

                                            try {

                                                foreach (ExtensionHelper::getProductConfig(Server::findOrFail($server), $get('settings')) as $setting) {
                                                    // Easier to use dot notation for settings
                                                    $setting['name'] = 'settings.' . $setting['name'];
                                                    $settings[] = FilamentInput::convert($setting, true);
                                                }
                                            } catch (\Exception $e) {
                                                $settings[] = Forms\Components\Placeholder::make('error')->content($e->getMessage());
                                            }

                                            return $settings;
                                        }
                                    ),

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
            ])->defaultGroup('category.name');
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
