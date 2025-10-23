<?php

namespace App\Admin\Resources;

use App\Admin\Resources\NotificationTemplateResource\Pages\CreateNotificationTemplate;
use App\Admin\Resources\NotificationTemplateResource\Pages\EditNotificationTemplate;
use App\Admin\Resources\NotificationTemplateResource\Pages\ListNotificationTemplates;
use App\Enums\NotificationEnabledStatus;
use App\Models\NotificationTemplate;
use Filament\Actions\EditAction;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class NotificationTemplateResource extends Resource
{
    protected static ?string $model = NotificationTemplate::class;

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
                Toggle::make('enabled')
                    ->required(),
                TextInput::make('edit_preference_message')
                    ->label('Edit Preference Message')
                    ->hint('This message will be shown to users when they edit their notification preferences for this template.')
                    ->columnSpanFull(),
                Section::make('Email Template')
                    ->description('Define the subject and body of the email template. You can use either Markdown or HTML for the body content.')
                    ->columns(2)
                    ->columnSpanFull()
                    ->collapsible()
                    ->schema([
                        TextInput::make('subject')
                            ->required()
                            ->maxLength(255),
                        Select::make('mail_enabled')
                            ->label('Mail Enabled')
                            ->options([
                                NotificationEnabledStatus::ChoiceOn->value => 'User Choice, Default On',
                                NotificationEnabledStatus::ChoiceOff->value => 'User Choice, Default Off',
                                NotificationEnabledStatus::Force->value => 'Force On',
                                NotificationEnabledStatus::Never->value => 'Force Off',
                            ])
                            ->required(),
                        MarkdownEditor::make('body')
                            ->hint('Use either Markdown or HTML to compose the email body.')
                            ->disableAllToolbarButtons()
                            ->required()
                            ->columnSpanFull(),
                        TagsInput::make('cc')
                            ->placeholder('mail@example.com')
                            ->nestedRecursiveRules(['required', 'email']),
                        TagsInput::make('bcc')
                            ->nestedRecursiveRules(['required', 'email'])
                            ->placeholder('mail@example.com'),
                    ]),
                Section::make('In-App Notification (push)')
                    ->description('Define the title and body of the in-app notification that users will receive.')
                    ->columns(2)
                    ->columnSpanFull()
                    ->collapsible()
                    ->schema([
                        TextInput::make('in_app_title')
                            ->label('In-App Title')
                            ->required()
                            ->disabled(fn (Get $get) => $get('in_app_enabled') === NotificationEnabledStatus::Never->value)
                            ->maxLength(255),
                        Select::make('in_app_enabled')
                            ->label('In-App Enabled')
                            ->options([
                                NotificationEnabledStatus::ChoiceOn->value => 'User Choice, Default On',
                                NotificationEnabledStatus::ChoiceOff->value => 'User Choice, Default Off',
                                NotificationEnabledStatus::Force->value => 'Force On',
                                NotificationEnabledStatus::Never->value => 'Force Off',
                            ])
                            ->live()
                            ->required(),
                        TextInput::make('in_app_body')
                            ->label('In-App Body')
                            ->required()
                            ->disabled(fn (Get $get) => $get('in_app_enabled') === NotificationEnabledStatus::Never->value)
                            ->columnSpanFull(),
                        TextInput::make('in_app_url')
                            ->label('In-App URL')
                            ->maxLength(255)
                            ->placeholder('{{ route("invoices.show", $invoice) }}')
                            ->hint('Supports dynamic variables like {{ route("invoices.show", $invoice) }}')
                            ->disabled(fn (Get $get) => $get('in_app_enabled') === NotificationEnabledStatus::Never->value)
                            ->columnSpanFull(),
                    ]),

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
            'index' => ListNotificationTemplates::route('/'),
            'create' => CreateNotificationTemplate::route('/create'),
            'edit' => EditNotificationTemplate::route('/{record}/edit'),
        ];
    }
}
