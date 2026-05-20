<?php

namespace App\Admin\Resources;

use App\Admin\Components\UserComponent;
use App\Admin\Resources\OrderResource\Pages\CreateOrder;
use App\Admin\Resources\OrderResource\Pages\EditOrder;
use App\Admin\Resources\OrderResource\Pages\ListOrders;
use App\Admin\Resources\OrderResource\RelationManagers\ServiceRelationManager;
use App\Helpers\ExtensionHelper;
use App\Models\Currency;
use App\Models\Order;
use App\Models\Product;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    public static function getNavigationLabel(): string
    {
        return __('orders.orders');
    }

    public static function getModelLabel(): string
    {
        return __('orders.order_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('orders.orders_plural_label');
    }

    protected static string|\BackedEnum|null $navigationIcon = 'ri-shopping-bag-4-line';

    protected static string|\BackedEnum|null $activeNavigationIcon = 'ri-shopping-bag-4-fill';

    public static string|\UnitEnum|null $navigationGroup = 'Administration';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                UserComponent::make('user_id')
                    ->afterStateUpdated(function (Set $set, Get $get) {
                        // update all the services user_id
                        $set('services', collect($get('services'))->map(fn ($service) => array_merge($service, ['user_id' => $get('user_id')]))->toArray());
                    }),
                Select::make('currency_code')
                    ->label(__('orders.currency'))
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (Set $set, Get $get) {
                        // update all the services currency_code
                        $set('services', collect($get('services'))->map(fn ($service) => array_merge($service, ['currency_code' => $get('currency_code')]))->toArray());
                    })
                    ->options(Currency::query()->pluck('code', 'code'))
                    ->helperText(__('orders.currency_helper'))
                    ->placeholder(__('orders.currency_placeholder')),
                Repeater::make('services')
                    ->relationship('services')
                    ->label(__('orders.services'))
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        Hidden::make('user_id')
                            ->default(fn (Get $get) => $get('../../user_id')),
                        Hidden::make('currency_code')
                            ->default(fn (Get $get) => $get('../../currency_code')),
                        Select::make('product_id')
                            ->label(__('orders.product'))
                            ->required()
                            ->relationship(
                                name: 'product',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (Builder $query) => $query->with('category')
                            )
                            ->getOptionLabelFromRecordUsing(fn (Product $product) => "{$product->name} - {$product->category->name} (#{$product->id})")
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(fn (Set $set) => $set('plan_id', null))
                            ->placeholder(__('orders.product_placeholder')),
                        Select::make('plan_id')
                            ->label(__('orders.plan'))
                            ->required()
                            ->relationship('plan', 'name', fn (Builder $query, Get $get) => $query->where('priceable_type', Product::class)->where('priceable_id', $get('product_id')))
                            ->searchable()
                            ->preload()
                            ->live()
                            ->disabled(fn (Get $get) => (!$get('product_id') || !$get('../../currency_code')))
                            ->afterStateUpdated(function (Set $set, Get $get) {
                                if (!$get('product_id') || !$get('plan_id') || !$get('../../currency_code')) {
                                    return;
                                }
                                // Update the price when the plan changes
                                $plan = Product::find($get('product_id'))->plans->find($get('plan_id'))->prices->where('currency_code', $get('../../currency_code'))->first();
                                if (!$plan) {
                                    return;
                                }
                                $set('price', $plan->price);
                            })
                            ->placeholder(__('orders.plan_placeholder')),
                        TextInput::make('quantity')
                            ->label(__('orders.quantity'))
                            ->required()
                            ->placeholder(__('orders.quantity_placeholder')),
                        TextInput::make('price')
                            ->suffix(fn (Component $component, Get $get) => $component->getRecord()?->currency->suffix ?? Currency::where('code', $get('../../currency_code'))->first()?->suffix)
                            ->prefix(fn (Component $component, Get $get) => $component->getRecord()?->currency->prefix ?? Currency::where('code', $get('../../currency_code'))->first()?->prefix)
                            ->label(__('orders.price'))
                            ->required()
                            ->mask(RawJs::make(
                                <<<'JS'
                                    $money($input, '.', '', 2)
                                JS
                            ))
                            ->placeholder(__('orders.price_placeholder')),
                        TextInput::make('subscription_id')
                            ->label(__('orders.subscription_id'))
                            ->nullable()
                            ->placeholder(__('orders.subscription_id_placeholder')),
                        Hidden::make('cancel_subscription')
                            ->hintActions([
                                Action::make('Cancel Subscription ID')
                                    ->action(function (Component $component) {
                                        if (ExtensionHelper::cancelSubscription($component->getRecord())) {
                                            Notification::make('Subscription Cancelled')
                                                ->title(__('orders.cancel_subscription_success'))
                                                ->success()
                                                ->send();
                                        } else {
                                            Notification::make('Subscription Not Cancelled')
                                                ->title(__('orders.cancel_subscription_error'))
                                                ->error()
                                                ->send();
                                        }
                                        // Update the record to remove the subscription ID
                                        $component->getRecord()->update(['subscription_id' => null]);
                                    })
                                    ->requiresConfirmation()
                                    ->label(__('orders.cancel_subscription'))
                                    ->hidden(fn (Component $component) => !$component->getRecord()?->subscription_id),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label(__('orders.id'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label(__('orders.user'))
                    ->searchable(query: fn (Builder $query, $search) => $query->whereHas('user', fn (Builder $query) => $query->where('first_name', 'like', "%$search%")->orWhere('last_name', 'like', "%$search%"))),
                TextColumn::make('currency.code')
                    ->label(__('orders.currency'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('formattedTotal')
                    ->label(__('orders.total')),
                TextColumn::make('updated_at')
                    ->label(__('orders.updated_at'))
                    ->searchable()
                    ->sortable(),
            ])
            ->defaultSort(function (Builder $query): Builder {
                return $query
                    ->orderBy('id', 'desc');
            })
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

    public static function getRelations(): array
    {
        return [
            ServiceRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrders::route('/'),
            'create' => CreateOrder::route('/create'),
            'edit' => EditOrder::route('/{record}/edit'),
        ];
    }
}
