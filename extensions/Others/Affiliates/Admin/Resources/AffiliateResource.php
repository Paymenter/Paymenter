<?php

namespace Paymenter\Extensions\Others\Affiliates\Admin\Resources;

use App\Admin\Resources\UserResource;
use App\Helpers\ExtensionHelper;
use App\Models\User;
use Paymenter\Extensions\Others\Affiliates\Admin\Resources\AffiliateResource\Pages;
use Paymenter\Extensions\Others\Affiliates\Admin\Resources\AffiliateResource\RelationManagers;
use Paymenter\Extensions\Others\Affiliates\Models\Affiliate;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;

class AffiliateResource extends Resource
{
    protected static ?string $model = Affiliate::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = 'Administration';

    public static function form(Form $form): Form
    {
        $extension = ExtensionHelper::getExtension('other', 'Affiliates');

        return $form
            ->schema([
                Toggle::make('enabled')->default(true)->columnSpanFull(),
                Select::make('user_id')
                    ->label('User')
                    ->relationship('user', 'id')
                    ->searchable()
                    ->preload()
                    ->getOptionLabelFromRecordUsing(fn($record) => $record->name)
                    ->getSearchResultsUsing(fn(string $search): array => User::where('first_name', 'like', "%$search%")->orWhere('last_name', 'like', "%$search%")->limit(50)->pluck('name', 'id')->toArray())
                    ->hint(fn($get) => $get('user_id') ? new HtmlString('<a href="' . UserResource::getUrl('edit', ['record' => $get('user_id')]) . '" target="_blank">Go to User</a>') : null)
                    ->live()
                    ->required(),
                TextInput::make('code')
                    ->label('Referral Code')
                    ->required()
                    ->minLength(5)
                    ->maxLength(20)
                    ->unique(ignoreRecord: true),
                TextInput::make('reward')
                    ->label('Affiliate Reward')
                    ->helperText("Percentage of the purchase amount the affiliated user would receive as a reward.")
                    ->placeholder($extension->config('default_reward'))
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100)
                    ->suffix('%'),
                TextInput::make('discount')
                    ->label('Affiliate Discount')
                    ->helperText('Discount percentage on products for the affiliated user.')
                    ->placeholder($extension->config('default_discount'))
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
                    if (count($affiliate->earnings) <= 0) return null;

                    return "Earnings - " . implode(', ', array_map(function ($key, $value) {
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
            RelationManagers\AffiliatesRelationManager::class
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
