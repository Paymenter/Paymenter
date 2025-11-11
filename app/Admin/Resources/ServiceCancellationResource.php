<?php

namespace App\Admin\Resources;

use App\Admin\Clusters\Services;
use App\Admin\Resources\ServiceCancellationResource\Pages\CreateServiceCancellation;
use App\Admin\Resources\ServiceCancellationResource\Pages\EditServiceCancellation;
use App\Admin\Resources\ServiceCancellationResource\Pages\ListServiceCancellations;
use App\Models\ServiceCancellation;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class ServiceCancellationResource extends Resource
{
    protected static ?string $model = ServiceCancellation::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = Services::class;

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('service_id')
                    ->relationship('service', 'id', fn (Builder $query) => $query->where('status', 'active'))
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->product->name . ' - ' . $record->plan->name . '  #' . $record->id . ($record->user ? ' (' . $record->user->email . ')' : ''))
                    ->searchable()
                    ->preload()
                    ->disabledOn('edit')
                    ->hint(fn ($get) => $get('service_id') ? new HtmlString('<a href="' . ServiceResource::getUrl('edit', ['record' => $get('service_id')]) . '" target="_blank">Go to Service</a>') : null)
                    ->required(),
                TextInput::make('reason')
                    ->maxLength(255)
                    ->default(null),
                Select::make('type')
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
                TextColumn::make('service_id')
                    ->formatStateUsing(fn ($record) => $record->service->product->name . ' - ' . $record->service->plan->name . '  #' . $record->service->id . ($record->service?->user ? ' (' . $record->service->user->email . ')' : ''))
                    ->url(fn ($record) => ServiceResource::getUrl('edit', ['record' => $record->service_id]))
                    ->sortable(),
                TextColumn::make('reason')
                    ->searchable(),
                TextColumn::make('type'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => ListServiceCancellations::route('/'),
            'create' => CreateServiceCancellation::route('/create'),
            'edit' => EditServiceCancellation::route('/{record}/edit'),
        ];
    }
}
