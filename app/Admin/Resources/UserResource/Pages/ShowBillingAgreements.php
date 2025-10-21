<?php

namespace App\Admin\Resources\UserResource\Pages;

use App\Admin\Resources\GatewayResource;
use App\Admin\Resources\UserResource;
use App\Models\Currency;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Table;

class ShowBillingAgreements extends ManageRelatedRecords
{
    protected static string $resource = UserResource::class;

    protected static string $relationship = 'billingAgreements';

    protected static string|\BackedEnum|null $navigationIcon = 'ri-bank-card-line';

    public static function getNavigationLabel(): string
    {
        return 'Billing Agreements';
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Name'),
                TextColumn::make('external_reference')
                    ->label('External Reference'),
                TextColumn::make('gateway.name')
                    ->url(fn($record) => GatewayResource::getUrl('edit', ['record' => $record->gateway_id]))
                    ->label('Gateway'),
            ]);
    }
}
