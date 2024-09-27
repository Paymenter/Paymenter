<?php

namespace App\Admin\Resources;

use App\Admin\Resources\OrderResource\Pages;
use App\Admin\Resources\OrderResource\RelationManagers;
use App\Helpers\ExtensionHelper;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Component;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    public static ?string $navigationGroup = 'Administration';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('User')
                    ->relationship('user', 'id')
                    ->searchable()
                    ->preload()
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->name)
                    ->getSearchResultsUsing(fn (string $search): array => User::where('first_name', 'like', "%$search%")->orWhere('last_name', 'like', "%$search%")->limit(50)->pluck('name', 'id')->toArray())
                    ->required(),
                Forms\Components\Select::make('currency_code')
                    ->label('Currency')
                    ->required()
                    ->relationship('currency', 'code')
                    ->helperText('Does not convert the price, only displays the currency symbol')
                    ->placeholder('Select the currency'),
                Forms\Components\Repeater::make('services')
                    ->relationship('services')
                    ->label('Services')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('product_id')
                            ->label('Product')
                            ->required()
                            ->options(Product::all()->pluck('name', 'id')->toArray())
                            ->searchable()
                            ->live()
                            ->afterStateUpdated(fn (Set $set) => $set('plan_id', null))
                            ->placeholder('Select the product'),
                        Forms\Components\Select::make('plan_id')
                            ->label('Plan')
                            ->required()
                            ->relationship('plan', 'name', fn (Builder $query, Get $get) => $query->where('priceable_type', Product::class)->where('priceable_id', $get('product_id')))
                            ->searchable()
                            ->preload()
                            ->live()
                            ->disabled(fn (Get $get) => !$get('product_id') || !$get('currency_code'))
                            ->afterStateUpdated(function (Set $set, Get $get) {
                                // Update the price when the plan changes
                                $plan = Product::find($get('product_id'))->plans->find($get('plan_id'))->prices->where('currency_code', $get('../currency_code'))->first();
                                $set('price', $plan->price);
                            })
                            ->placeholder('Select the plan'),
                        Forms\Components\TextInput::make('quantity')
                            ->label('Quantity')
                            ->required()
                            ->placeholder('Enter the quantity'),
                        Forms\Components\TextInput::make('price')
                            ->suffix(fn (Component $component) => $component->getRecord()?->currency->suffix)
                            ->prefix(fn (Component $component) => $component->getRecord()?->currency->prefix)
                            ->label('Price')
                            ->required()
                            ->mask(RawJs::make(
                                <<<'JS'
                                    $money($input, '.', '', 2)
                                JS
                            ))
                            ->placeholder('Enter the price'),
                        Forms\Components\TextInput::make('subscription_id')
                            ->label('Subscription ID')
                            ->nullable()
                            ->placeholder('Enter the subscription ID')
                            ->hintActions([
                                Action::make('Cancel Subscription ID')
                                    ->action(function (Component $component) {
                                        if (ExtensionHelper::cancelSubscription($component->getRecord())) {
                                            Notification::make('Subscription Cancelled')
                                                ->title('The subscription has been successfully cancelled')
                                                ->success()
                                                ->send();
                                        } else {
                                            Notification::make('Subscription Not Cancelled')
                                                ->title('The subscription could not be cancelled')
                                                ->error()
                                                ->send();
                                        }
                                        // Update the record to remove the subscription ID
                                        $component->getRecord()->update(['subscription_id' => null]);
                                    })
                                    ->requiresConfirmation()
                                    ->label('Cancel Subscription')
                                    ->hidden(fn (Component $component) => !$component->getRecord()?->subscription_id),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('currency.code')
                    ->label('Currency')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('formattedTotal')
                    ->label('Total')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated At')
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
            RelationManagers\ServiceRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
