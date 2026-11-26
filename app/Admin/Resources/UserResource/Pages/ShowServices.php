<?php

namespace App\Admin\Resources\UserResource\Pages;

use App\Admin\Resources\ServiceResource;
use App\Admin\Resources\UserResource;
use App\Models\Category;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ShowServices extends ManageRelatedRecords
{
    protected static string $resource = UserResource::class;

    protected static string $relationship = 'services';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shopping-cart';

    public static function getNavigationLabel(): string
    {
        return 'Products/Services';
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('product.name')
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('product.name')
                    ->searchable(),
                TextColumn::make('product.category.name')
                    ->label('Category'),
                TextColumn::make('quantity'),
                TextColumn::make('formattedPrice')->label('Price'),
                TextColumn::make('status')
                    ->sortable()
                    ->badge()
                    ->formatStateUsing(fn (string $state) => ucfirst($state))
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'pending' => 'warning',
                        'suspended' => 'danger',
                        'cancelled' => 'gray',
                        default => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'pending' => 'Pending',
                        'suspended' => 'Suspended',
                        'cancelled' => 'Cancelled',
                    ]),
                SelectFilter::make('category')
                    ->options(fn () => Category::whereHas('products.services', fn (Builder $query) => $query->where('user_id', $this->getOwnerRecord()->id))
                        ->orderBy('name')
                        ->pluck('name', 'id'))
                    ->query(fn (Builder $query, array $data) => $query->when(
                        $data['value'],
                        fn (Builder $query, $category) => $query->whereHas('product', fn (Builder $query) => $query->where('category_id', $category))
                    )),
            ])
            ->recordActions([
                ViewAction::make()->url(fn ($record) => ServiceResource::getUrl('edit', ['record' => $record])),
            ]);
    }
}
