<?php

namespace App\Admin\Resources;

use App\Admin\Clusters\Services;
use App\Admin\Resources\ServiceCancellationResource\Pages;
use App\Models\ServiceCancellation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ServiceCancellationResource extends Resource
{
    protected static ?string $model = ServiceCancellation::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = Services::class;

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('service_id')
                    ->relationship('service', 'id', fn (Builder $query) => $query->where('status', 'active'))
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->product->name . ' - ' . $record->plan->name . '  #' . $record->id . ($record->order && $record->order->user ? ' (' . $record->order->user->email . ')' : ''))
                    ->searchable()
                    ->preload()
                    ->disabledOn('edit')
                    ->required(),
                Forms\Components\TextInput::make('reason')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\Select::make('type')
                    ->options([
                        'end_of_period' => 'End of Period',
                        'immediate' => 'Immediate',
                    ])
                    ->disabledOn('edit')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('service_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('reason')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type'),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServiceCancellations::route('/'),
            'create' => Pages\CreateServiceCancellation::route('/create'),
            'edit' => Pages\EditServiceCancellation::route('/{record}/edit'),
        ];
    }
}
