<?php

namespace App\Admin\Resources;

use App\Admin\Resources\CouponResource\Pages;
use App\Admin\Resources\CouponResource\RelationManagers;
use App\Models\Coupon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Table;

class CouponResource extends Resource
{
    protected static ?string $model = Coupon::class;

    protected static ?string $navigationIcon = 'ri-coupon-line';

    protected static ?string $activeNavigationIcon = 'ri-coupon-fill';

    protected static ?string $navigationGroup = 'Configuration';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code')
                    ->label('Code')
                    ->required()
                    ->maxLength(255)
                    ->unique(static::getModel(), 'code', ignoreRecord: true)
                    ->placeholder('Enter the code of the coupon'),

                Forms\Components\TextInput::make('value')
                    ->label('Value')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(fn (Get $get) => $get('type') === 'percentage' ? 100 : null)
                    ->mask(RawJs::make(
                        <<<'JS'
                            $money($input, '.', '', 2)
                        JS
                    ))
                    ->hidden(fn (Get $get) => $get('type') === 'free_setup')
                    ->suffix(fn (Get $get) => $get('type') === 'percentage' ? '%' : config('settings.default_currency'))
                    ->placeholder('Enter the value of the coupon'),

                Forms\Components\Select::make('type')
                    ->label('Type')
                    ->required()
                    ->default('percentage')
                    ->live()
                    ->options([
                        'percentage' => 'Percentage',
                        'fixed' => 'Fixed amount',
                        'free_setup' => 'Free setup',
                    ])
                    ->placeholder('Select the type of the coupon'),

                Forms\Components\TextInput::make('recurring')
                    ->label('Recurring')
                    ->numeric()
                    ->nullable()
                    ->minValue(0)
                    ->hidden(fn (Get $get) => $get('type') === 'free_setup')
                    ->placeholder('How many billing cycles the discount will be applied')
                    ->helperText('Enter 0 to apply it to all billing cycles, 1 to apply it only to the first billing cycle, etc.'),

                Forms\Components\TextInput::make('max_uses')
                    ->label('Max Uses')
                    ->numeric()
                    ->minValue(0)
                    ->placeholder('Enter the maximum number of total uses of the coupon'),

                Forms\Components\TextInput::make('max_uses_per_user')
                    ->label('Max Uses Per User')
                    ->numeric()
                    ->minValue(0)
                    ->placeholder('Enter the maximum number of uses per user'),

                Forms\Components\DatePicker::make('starts_at')
                    ->label('Starts At'),

                Forms\Components\DatePicker::make('expires_at')
                    ->label('Expires At'),

                Forms\Components\Select::make('products')
                    ->label('Products')
                    ->relationship('products', 'name')
                    ->multiple()
                    ->preload()
                    ->placeholder('Select the products that this coupon applies to')
                    ->hint('Leave empty to apply the coupon to all products'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')->searchable(),
                Tables\Columns\TextColumn::make('value')->searchable()->formatStateUsing(fn ($record) => $record->value . ($record->type === 'percentage' ? '%' : config('settings.default_currency'))),
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
            RelationManagers\ServicesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCoupons::route('/'),
            'create' => Pages\CreateCoupon::route('/create'),
            'edit' => Pages\EditCoupon::route('/{record}/edit'),
        ];
    }
}
