<?php

namespace App\Admin\Components;

use App\Admin\Resources\UserResource;
use App\Models\User;
use Filament\Forms\Components\Select;
use Illuminate\Support\HtmlString;

class UserComponent extends Select
{
    public static function make(?string $name = null): static
    {
        return parent::make($name)
            ->label('User')
            ->relationship('user', 'id')
            ->searchable()
            ->preload()
            ->getOptionLabelFromRecordUsing(fn ($record) => $record->name . ' (' . $record->email . ')')
            ->getSearchResultsUsing(fn (string $search): array => User::where('first_name', 'like', "%$search%")
                ->orWhere('last_name', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%")
                ->orWhere('id', 'like', "%$search%")
                ->limit(50)
                ->get()
                ->mapWithKeys(fn ($user) => [$user->id => $user->name . ' (' . $user->email . ')'])
                ->toArray())
            ->hint(fn ($get) => $get('user_id') ? new HtmlString('<a href="' . UserResource::getUrl('edit', ['record' => $get('user_id')]) . '" target="_blank">Go to User</a>') : null)
            ->live()
            ->required();
    }
}
