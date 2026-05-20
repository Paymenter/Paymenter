<?php

namespace App\Admin\Resources;

use App\Admin\Resources\CouponResource\Pages\CreateCoupon;
use App\Admin\Resources\CouponResource\Pages\EditCoupon;
use App\Admin\Resources\CouponResource\Pages\ListCoupons;
use App\Admin\Resources\CouponResource\RelationManagers\ServicesRelationManager;
use App\Models\Coupon;
use App\Models\Product;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CouponResource extends Resource
{
    protected static ?string $model = Coupon::class;

    public static function getNavigationLabel(): string
    {
        return __('coupons.coupons');
    }

    public static function getModelLabel(): string
    {
        return __('coupons.coupon_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('coupons.coupons_plural_label');
    }

    protected static string|\BackedEnum|null $navigationIcon = 'ri-coupon-line';

    protected static string|\BackedEnum|null $activeNavigationIcon = 'ri-coupon-fill';

    protected static string|\UnitEnum|null $navigationGroup = 'Configuration';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->label(__('coupons.code'))
                    ->required()
                    ->maxLength(255)
                    ->unique(static::getModel(), 'code', ignoreRecord: true)
                    ->placeholder(__('coupons.enter_code')),

                TextInput::make('value')
                    ->label(__('coupons.value'))
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(fn (Get $get) => $get('type') === 'percentage' ? 100 : null)
                    ->mask(RawJs::make(
                        <<<'JS'
                            $money($input, '.', '', 2)
                        JS
                    ))
                    ->suffix(fn (Get $get) => $get('type') === 'percentage' ? '%' : config('settings.default_currency'))
                    ->placeholder(__('coupons.enter_value')),

                Select::make('type')
                    ->label(__('coupons.type'))
                    ->required()
                    ->default('percentage')
                    ->live()
                    ->options([
                        'percentage' => __('coupons.types.percentage'),
                        'fixed' => __('coupons.types.fixed'),
                    ])
                    ->placeholder(__('coupons.select_type')),
                Select::make('applies_to')
                    ->label(__('coupons.applies_to'))
                    ->required()
                    ->default('all')
                    ->options([
                        'all' => __('coupons.applies_to_options.all'),
                        'price' => __('coupons.applies_to_options.price'),
                        'setup_fee' => __('coupons.applies_to_options.setup_fee'),
                    ]),

                TextInput::make('recurring')
                    ->label(__('coupons.recurring'))
                    ->numeric()
                    ->nullable()
                    ->minValue(0)
                    ->hidden(fn (Get $get) => $get('applies_to') === 'free_setup')
                    ->placeholder(__('coupons.recurring_placeholder'))
                    ->helperText(__('coupons.recurring_helper')),

                TextInput::make('max_uses')
                    ->label(__('coupons.max_uses'))
                    ->numeric()
                    ->minValue(0)
                    ->placeholder(__('coupons.max_uses_placeholder')),

                TextInput::make('max_uses_per_user')
                    ->label(__('coupons.max_uses_per_user'))
                    ->numeric()
                    ->minValue(0)
                    ->placeholder(__('coupons.max_uses_per_user_placeholder')),

                DatePicker::make('starts_at')
                    ->label(__('coupons.starts_at')),

                DatePicker::make('expires_at')
                    ->label(__('coupons.expires_at')),

                Select::make('products')
                    ->label(__('coupons.products'))
                    ->relationship(
                        name: 'products',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query) => $query->with('category')
                    )
                    ->getOptionLabelFromRecordUsing(fn (Product $record) => "{$record->name} - {$record->category->name} (#{$record->id})")
                    ->multiple()
                    ->preload()
                    ->placeholder(__('coupons.products_placeholder'))
                    ->hint(__('coupons.products_hint')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label(__('coupons.code'))
                    ->searchable(),
                TextColumn::make('value')
                    ->label(__('coupons.value'))
                    ->searchable()
                    ->formatStateUsing(fn ($record) => $record->value . ($record->type === 'percentage' ? '%' : config('settings.default_currency'))),
            ])
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
            ServicesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCoupons::route('/'),
            'create' => CreateCoupon::route('/create'),
            'edit' => EditCoupon::route('/{record}/edit'),
        ];
    }
}
