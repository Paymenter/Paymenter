<?php

namespace Paymenter\Extensions\Others\Affiliates\Admin\Resources;

use App\Admin\Components\UserComponent;
use App\Helpers\ExtensionHelper;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Paymenter\Extensions\Others\Affiliates\Admin\Resources\AffiliateResource\Pages\EditAffiliate;
use Paymenter\Extensions\Others\Affiliates\Admin\Resources\AffiliateResource\Pages\ListAffiliates;
use Paymenter\Extensions\Others\Affiliates\Admin\Resources\AffiliateResource\RelationManagers\AffiliatesRelationManager;
use Paymenter\Extensions\Others\Affiliates\Models\Affiliate;

class AffiliateResource extends Resource
{
    protected static ?string $model = Affiliate::class;

    protected static string|\BackedEnum|null $navigationIcon = 'ri-hand-coin-line';

    protected static string|\BackedEnum|null $activeNavigationIcon = 'ri-hand-coin-fill';

    protected static string|\UnitEnum|null $navigationGroup = 'Administration';

    public static function form(Schema $schema): Schema
    {
        $extension = ExtensionHelper::getExtension('other', 'Affiliates');

        return $schema
            ->components([
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
            AffiliatesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAffiliates::route('/'),
            'edit' => EditAffiliate::route('/{record}/edit'),
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
