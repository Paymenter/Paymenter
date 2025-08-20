<?php

namespace App\Admin\Resources;

use App\Admin\Resources\EmailTemplateResource\Pages\CreateEmailTemplate;
use App\Admin\Resources\EmailTemplateResource\Pages\EditEmailTemplate;
use App\Admin\Resources\EmailTemplateResource\Pages\ListEmailTemplates;
use App\Models\EmailTemplate;
use Filament\Actions\EditAction;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EmailTemplateResource extends Resource
{
    protected static ?string $model = EmailTemplate::class;

    protected static string|\BackedEnum|null $navigationIcon = 'ri-mail-settings-line';

    protected static string|\BackedEnum|null $activeNavigationIcon = 'ri-mail-settings-fill';

    protected static string|\UnitEnum|null $navigationGroup = 'Other';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('key')
                    ->required()
                    ->disabledOn('edit')
                    ->maxLength(255),
                TextInput::make('subject')
                    ->required()
                    ->maxLength(255),
                Toggle::make('enabled')
                    ->required(),
                MarkdownEditor::make('body')
                    ->hint('Use either Markdown or HTML to compose the email body.')
                    ->disableAllToolbarButtons()
                    ->required()
                    ->columnSpanFull(),
                TagsInput::make('cc')
                    ->placeholder('mail@example.com')
                    ->nestedRecursiveRules(['required', 'email'])
                    ->columnSpanFull(),
                TagsInput::make('bcc')
                    ->nestedRecursiveRules(['required', 'email'])
                    ->placeholder('mail@example.com')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('subject')
                    ->searchable(),
                IconColumn::make('enabled')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
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
            'index' => ListEmailTemplates::route('/'),
            'create' => CreateEmailTemplate::route('/create'),
            'edit' => EditEmailTemplate::route('/{record}/edit'),
        ];
    }
}
