<?php

namespace Paymenter\Extensions\Others\Affiliates\Admin\Resources\AffiliateResource\RelationManagers;

use App\Admin\Resources\UserResource;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Paymenter\Extensions\Others\Affiliates\Models\AffiliateReferral;

class ReferralsRelationManager extends RelationManager
{
    protected static string $relationship = 'referrals';

    protected static ?string $model = AffiliateReferral::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->label('User')
                    ->relationship(name: 'referredUser', titleAttribute: 'first_name', ignoreRecord: true)
                    ->searchable(['first_name', 'last_name'])
                    ->preload()
                    // Disallow referring the affiliate, or already referred user
                    ->disableOptionWhen(fn (string $value): bool => $this->getOwnerRecord()->user->id === (int) $value)
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->name)
                    ->hint(fn ($get) => $get('user_id') ? new HtmlString('<a href="' . UserResource::getUrl('edit', ['record' => $get('user_id')]) . '" target="_blank">Go to User</a>') : null)
                    ->live()
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('affiliate_id')
            ->columns([
                TextColumn::make('referredUser.name'),
                TextColumn::make('earnings')->numeric(),
                TextColumn::make('created_at')
                    ->label('Signed Up')
                    ->since()
                    ->dateTimeTooltip(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
