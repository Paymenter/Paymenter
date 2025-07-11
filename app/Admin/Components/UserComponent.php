<?php

namespace App\Admin\Components;

use App\Admin\Resources\UserResource;
use App\Models\User;
use Filament\Forms\Components\Select;

class UserComponent extends Select
{
    public static function make(string $name): static
    {
        return parent::make($name)
            ->label('User')
            ->relationship('user', 'id')
            ->searchable()
            ->preload()
            ->getOptionLabelFromRecordUsing(fn ($record) => $record->name . ' (' . $record->email . ')')
            ->getSearchResultsUsing(fn (string $search): array => User::where('first_name', 'like', "%$search%")
                ->orWhere('last_name', 'like', "%$search%")
                ->limit(50)
                ->get()
                ->mapWithKeys(fn ($user) => [$user->id => $user->name . ' (' . $user->email . ')'])
                ->toArray())
            ->hint(fn ($get) => $get('user_id') ? new \Illuminate\Support\HtmlString('<a href="' . UserResource::getUrl('edit', ['record' => $get('user_id')]) . '" target="_blank">Go to User</a>') : null)
            ->live()
            ->required();
    }
}
