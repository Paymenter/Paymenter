<?php

namespace Paymenter\Extensions\Others\Affiliates\Admin\Resources;

use App\Admin\Components\UserComponent;
use App\Helpers\ExtensionHelper;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Paymenter\Extensions\Others\Affiliates\Admin\Resources\AffiliateResource\Pages;
use Paymenter\Extensions\Others\Affiliates\Admin\Resources\AffiliateResource\RelationManagers;
use Paymenter\Extensions\Others\Affiliates\Models\Affiliate;

class AffiliateResource extends Resource
{
    protected static ?string $model = Affiliate::class;

    protected static ?string $navigationIcon = 'ri-hand-coin-line';

    protected static ?string $activeNavigationIcon = 'ri-hand-coin-fill';

    protected static ?string $navigationGroup = 'Administration';

    public static function form(Form $form): Form
    {
        $extension = ExtensionHelper::getExtension('other', 'Affiliates');

        return $form
            ->schema([
                Toggle::make('enabled')->default(true)->columnSpanFull(),
                UserComponent::make('user_id'),
                TextInput::make('code')
                    ->label('Referral Code')
                    ->required()
                    ->minLength(5)
                    ->maxLength(20)
                    ->unique(ignoreRecord: true),
                TextInput::make('reward')
                    ->label('Affiliate Reward')
                    ->helperText('Percentage of the purchase amount the affiliated user would receive as a reward.')
                    ->placeholder($extension->config('default_reward'))
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100)
                    ->suffix('%'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')->description(function (Affiliate $affiliate) {
                    if (count($affiliate->earnings) <= 0) {
                        return null;
                    }

                    return 'Earnings - ' . implode(', ', array_map(function ($key, $value) {
                        return "$key: $value";
                    }, array_keys($affiliate->earnings), $affiliate->earnings));
                }),
                TextColumn::make('code')
                    ->badge(),
                TextColumn::make('visitors')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('signups')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Signed Up')
                    ->since()
                    ->sortable()
                    ->dateTimeTooltip(),
            ])
            ->filters([])
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
            RelationManagers\AffiliatesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAffiliates::route('/'),
            'edit' => Pages\EditAffiliate::route('/{record}/edit'),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            $this->getCreateFormAction()
                ->formId('form'),
        ];
    }
}
