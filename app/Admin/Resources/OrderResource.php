<?php

namespace App\Admin\Resources;

use App\Admin\Resources\OrderResource\Pages;
use App\Helpers\ExtensionHelper;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Component;
use Filament\Forms\Form;
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

    public static function getNavigationBadge(): ?string
    {
        return OrderProduct::where('status', 'pending')->count() ?: null;
    }

    public static ?string $navigationGroup = 'Administration';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('User')
                    ->required()
                    ->relationship('user', 'name')
                    ->searchable()
                    ->placeholder('Select the user'),
                Forms\Components\Select::make('currency_code')
                    ->label('Currency')
                    ->required()
                    ->relationship('currency', 'code')
                    ->helperText('Does not convert the price, only displays the currency symbol')
                    ->placeholder('Select the currency'),
                Forms\Components\Repeater::make('orderProducts')
                    ->relationship('orderProducts')
                    ->label('Order Products')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('product_id')
                            ->label('Product')
                            ->required()
                            ->options(Product::all()->pluck('name', 'id')->toArray())
                            ->searchable()
                            ->placeholder('Select the product'),
                        Forms\Components\Select::make('plan_id')
                            ->label('Plan')
                            ->required()
                            ->relationship('plan', 'name')
                            ->searchable()
                            ->placeholder('Select the plan'),
                        Forms\Components\TextInput::make('quantity')
                            ->label('Quantity')
                            ->required()
                            ->placeholder('Enter the quantity'),
                        Forms\Components\TextInput::make('price')
                            ->suffix(fn (Component $component) => $component->getRecord()->currency->suffix)
                            ->prefix(fn (Component $component) => $component->getRecord()->currency->prefix)
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
                                    ->hidden(fn (Component $component) => !$component->getRecord()->subscription_id),
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
            //
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
