<?php

namespace App\Admin\Resources;

use App\Admin\Resources\OrderProductResource\Pages;
use App\Admin\Resources\OrderProductResource\RelationManagers;
use App\Helpers\ExtensionHelper;
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

class OrderProductResource extends Resource
{
    protected static ?string $model = OrderProduct::class;

    protected static ?string $navigationIcon = 'ri-service-line';

    public static ?string $label = 'Service';

    public static function getNavigationBadge(): ?string
    {
        return OrderProduct::where('status', 'pending')->count() ?: null;
    }

    public static ?string $navigationGroup = 'Administration';

    public static function form(Form $form): Form
    {
        return $form
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
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->required()
                    ->options([
                        // active, pending, suspended, cancelled
                        'active' => 'Active',
                        'pending' => 'Pending',
                        'suspended' => 'Suspended',
                        'cancelled' => 'Cancelled',
                    ])
                    ->default('pending'),
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
                    ->hintAction(
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
                    ),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Product')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->searchable()
                    ->sortable(),
            ])
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
            RelationManagers\InvoicesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrderProducts::route('/'),
            'create' => Pages\CreateOrderProduct::route('/create'),
            'edit' => Pages\EditOrderProduct::route('/{record}/edit'),
        ];
    }
}
