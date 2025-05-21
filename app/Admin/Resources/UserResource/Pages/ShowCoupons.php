<?php

namespace App\Admin\Resources\UserResource\Pages;

use App\Admin\Resources\UserResource;
use App\Models\Coupon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Support\Facades\Filament;

class ShowCoupons extends ManageRelatedRecords
{
    protected static string $resource = UserResource::class;
    protected static string $relationship = 'coupons';
    protected static string $view = 'filament.resources.user-resource.pages.show-coupons';
    protected static ?string $navigationIcon = 'ri-coupon-line';
    
    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()
            ->orWhereHas('services', function ($query) {
                $query->where('user_id', $this->getOwnerRecord()->id);
            });
    }

    public static function getNavigationLabel(): string
    {
        return 'Coupons';
    }

    protected function getTableQuery(): Builder
    {
        $user = $this->getOwnerRecord();
        $usedCouponIds = array_keys($user->coupon_usage ?? []);
        
        return Coupon::query()
            ->whereIn('id', $usedCouponIds)
            ->orWhereHas('services', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            });
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->color(fn (string $state): string => match ($state) {
                        'percentage' => 'success',
                        'fixed' => 'primary',
                        'setup' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('value')
                    ->formatStateUsing(function (Coupon $record) {
                        if ($record->type === 'percentage') {
                            return $record->value . '%';
                        }
                        
                        $defaultCurrency = config('settings.currency');
                        return currency($record->value, $record->services->first()?->currency_code ?? $defaultCurrency);
                    })
                    ->sortable(),
                Tables\Columns\TextInputColumn::make('usage_count')
                    ->label('Times Used')
                    ->getStateUsing(function (Coupon $record) {
                        $user = $this->getOwnerRecord();
                        return $user->coupon_usage[$record->id] ?? 0;
                    })
                    ->updateStateUsing(function (Coupon $record, $state) {
                        $user = $this->getOwnerRecord();
                        $couponUsage = $user->coupon_usage ?? [];
                        $couponUsage[$record->id] = (int)$state;
                        $user->coupon_usage = $couponUsage;
                        $user->save();
                    })
                    ->rules(['numeric', 'min:0'])
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_uses_per_user')
                    ->label('Max Uses/User')
                    ->formatStateUsing(fn (Coupon $record): string => $record->max_uses_per_user ?: '∞')
                    ->sortable(),
                Tables\Columns\TextColumn::make('expires_at')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Never'),
            ])
            ->filters([
                // Add filters if needed
            ]);
    }
}
