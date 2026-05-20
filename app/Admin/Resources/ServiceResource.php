<?php

namespace App\Admin\Resources;

use App\Admin\Clusters\Services;
use App\Admin\Components\UserComponent;
use App\Admin\Resources\Common\RelationManagers\PropertiesRelationManager;
use App\Admin\Resources\ServiceResource\Pages\CreateService;
use App\Admin\Resources\ServiceResource\Pages\EditService;
use App\Admin\Resources\ServiceResource\Pages\ListService;
use App\Admin\Resources\ServiceResource\RelationManagers\ConfigOptionsRelationManager;
use App\Admin\Resources\ServiceResource\RelationManagers\InvoicesRelationManager;
use App\Helpers\ExtensionHelper;
use App\Models\Currency;
use App\Models\Product;
use App\Models\Service;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    public static function getNavigationLabel(): string
    {
        return __('services.services');
    }

    public static function getModelLabel(): string
    {
        return __('services.service_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('services.services_plural_label');
    }

    protected static string|\BackedEnum|null $navigationIcon = 'ri-function-line';

    protected static string|\BackedEnum|null $activeNavigationIcon = 'ri-function-fill';

    public static function getNavigationBadge(): ?string
    {
        return Service::where('status', 'pending')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    protected static ?string $cluster = Services::class;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('product_id')
                    ->label(__('services.product'))
                    ->required()
                    ->relationship(
                        name: 'product',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query) => $query->with('category')
                    )
                    ->getOptionLabelFromRecordUsing(fn (Product $product) => "{$product->name} - {$product->category->name} (#{$product->id})")
                    ->searchable()
                    ->live()
                    ->preload()
                    ->placeholder(__('services.select_product')),
                Select::make('plan_id')
                    ->label(__('services.plan'))
                    ->required()
                    ->relationship('plan', 'name', fn (Builder $query, Get $get) => $query->where('priceable_id', $get('product_id'))->where('priceable_type', Product::class))
                    ->searchable()
                    ->preload()
                    ->disabled(fn (Get $get) => !$get('product_id'))
                    ->placeholder(__('services.select_plan')),
                UserComponent::make('user_id'),
                Select::make('status')
                    ->label(__('services.status'))
                    ->required()
                    ->options([
                        'active' => __('services.statuses.active'),
                        'pending' => __('services.statuses.pending'),
                        'suspended' => __('services.statuses.suspended'),
                        'cancelled' => __('services.statuses.cancelled'),
                    ])
                    ->default('pending'),
                TextInput::make('quantity')
                    ->label(__('services.quantity_label'))
                    ->required()
                    ->placeholder(__('services.enter_quantity')),
                DatePicker::make('expires_at')
                    ->label(__('services.expires_at'))
                    ->required(fn (Get $get) => $get('plan')?->type != 'one-time' && $get('plan')?->type != 'free' && $get('status') !== 'pending')
                    ->placeholder(__('services.select_expiration_date')),
                Select::make('coupon_id')
                    ->label(__('services.coupon'))
                    ->relationship('coupon', 'code')
                    ->searchable()
                    ->preload()
                    ->placeholder(__('services.select_coupon')),
                Select::make('currency_code')
                    ->label(__('orders.currency'))
                    ->options(function (Get $get, ?string $state) {
                        $pricing = collect($get('../../pricing'))->pluck('currency_code');
                        if ($state !== null) {
                            $pricing = $pricing->filter(function ($code) use ($state) {
                                return $code !== $state;
                            });
                        }
                        $pricing = $pricing->filter(function ($code) {
                            return $code !== null;
                        });

                        return Currency::whereNotIn('code', $pricing)->pluck('code', 'code');
                    })
                    ->live()
                    ->default(config('settings.default_currency'))
                    ->required(),
                TextInput::make('price')
                    ->required()
                    ->label(__('services.price_col'))
                    // Suffix based on chosen currency
                    ->prefix(fn (Get $get) => Currency::where('code', $get('currency_code'))->first()?->prefix)
                    ->suffix(fn (Get $get) => Currency::where('code', $get('currency_code'))->first()?->suffix)
                    ->live(onBlur: true)
                    ->mask(RawJs::make(
                        <<<'JS'
                            $money($input, '.', '', 2)
                        JS
                    ))
                    ->numeric()
                    ->minValue(0)
                    ->hintAction(
                        Action::make('recalculate_price')
                            ->action(function (Component $component, Service $service) {
                                if ($service) {
                                    Notification::make('price_recalculated')
                                        ->title(__('services.price_recalculated_title'))
                                        ->success()
                                        ->send();
                                    // Update the form field
                                    $component->state($service->calculatePrice());
                                }
                            })
                            ->label(__('services.recalculate_price'))
                            ->icon('ri-refresh-line')
                    ),
                Select::make('billing_agreement_id')
                    ->label(__('services.billing_agreement'))
                    ->relationship('billingAgreement', 'name', fn (Builder $query, Get $get) => $query->where('user_id', $get('user_id')))
                    ->searchable()
                    ->preload()
                    ->placeholder(__('services.select_billing_agreement_admin')),
                TextInput::make('subscription_id')
                    ->label(__('services.subscription_id'))
                    ->nullable()
                    ->placeholder(__('services.enter_subscription_id'))
                    ->hintAction(
                        Action::make('cancel_subscription')
                            ->action(function (Component $component) {
                                if (ExtensionHelper::cancelSubscription($component->getRecord())) {
                                    Notification::make('subscription_cancelled')
                                        ->title(__('services.subscription_cancelled_title'))
                                        ->success()
                                        ->send();
                                } else {
                                    Notification::make('subscription_not_cancelled')
                                        ->title(__('services.subscription_not_cancelled_title'))
                                        ->error()
                                        ->send();
                                }
                                // Update the record to remove the subscription ID
                                $component->getRecord()->update(['subscription_id' => null]);
                            })
                            ->requiresConfirmation()
                            ->label(__('services.cancel_subscription'))
                            ->hidden(fn (Component $component) => !$component->getRecord()?->subscription_id),
                    ),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label(__('services.id'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label(__('services.user'))
                    ->searchable(true, fn (Builder $query, string $search) => $query->whereHas('user', fn (Builder $query) => $query->where('first_name', 'like', "%$search%")->orWhere('last_name', 'like', "%$search%"))),
                TextColumn::make('product.name')
                    ->label(__('services.product'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (Service $record) => match ($record->status) {
                        'pending' => 'gray',
                        'active' => 'success',
                        'cancelled' => 'danger',
                        'suspended' => 'warning',
                    })
                    ->formatStateUsing(fn (string $state) => __('services.statuses.' . $state))
                    ->label(__('services.status'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('expires_at')
                    ->label(__('services.expires_at'))
                    ->date()
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('services.status'))
                    ->options([
                        'active' => __('services.statuses.active'),
                        'pending' => __('services.statuses.pending'),
                        'suspended' => __('services.statuses.suspended'),
                        'cancelled' => __('services.statuses.cancelled'),
                    ]),
                SelectFilter::make('user')
                    ->label(__('services.user'))
                    ->relationship('user', 'id')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->name . ' (' . $record->email . ')')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('product')
                    ->label(__('services.product'))
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload(),
                Filter::make('expires_at')
                    ->form([
                        DatePicker::make('expires_from')->label(__('services.expires_from')),
                        DatePicker::make('expires_until')->label(__('services.expires_until')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['expires_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('expires_at', '>=', $date),
                            )
                            ->when(
                                $data['expires_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('expires_at', '<=', $date),
                            );
                    }),
            ])
            ->defaultSort(function (Builder $query): Builder {
                return $query
                    ->orderBy('id', 'desc');
            })
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
            InvoicesRelationManager::class,
            PropertiesRelationManager::class,
            ConfigOptionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListService::route('/'),
            'create' => CreateService::route('/create'),
            'edit' => EditService::route('/{record}/edit'),
        ];
    }
}
