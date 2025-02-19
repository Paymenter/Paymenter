<?php

namespace App\Admin\Resources;

use App\Admin\Resources\EmailTemplateResource\Pages;
use App\Models\EmailTemplate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EmailTemplateResource extends Resource
{
    protected static ?string $model = EmailTemplate::class;

    protected static ?string $navigationIcon = 'ri-mail-settings-line';

    protected static ?string $activeNavigationIcon = 'ri-mail-settings-fill';

    protected static ?string $navigationGroup = 'Other';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('key')
                    ->required()
                    ->disabledOn('edit')
                    ->maxLength(255),
                Forms\Components\TextInput::make('subject')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Toggle::make('enabled')
                    ->required(),
                Forms\Components\MarkdownEditor::make('body')
                    ->hint('Use either Markdown or HTML to compose the email body.')
                    ->disableAllToolbarButtons()
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TagsInput::make('cc')
                    ->placeholder('mail@example.com')
                    ->nestedRecursiveRules(['required', 'email'])
                    ->columnSpanFull(),
                Forms\Components\TagsInput::make('bcc')
                    ->nestedRecursiveRules(['required', 'email'])
                    ->placeholder('mail@example.com')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('subject')
                    ->searchable(),
                Tables\Columns\IconColumn::make('enabled')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListEmailTemplates::route('/'),
            'create' => Pages\CreateEmailTemplate::route('/create'),
            'edit' => Pages\EditEmailTemplate::route('/{record}/edit'),
        ];
    }
}
