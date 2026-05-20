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

    public static function getNavigationLabel(): string
    {
        return __('notifications.notification_templates');
    }

    public static function getModelLabel(): string
    {
        return __('notifications.notification_template');
    }

    public static function getPluralModelLabel(): string
    {
        return __('notifications.notification_templates');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('key')
                    ->label(__('notifications.key'))
                    ->required()
                    ->disabledOn('edit')
                    ->maxLength(255),
                Toggle::make('enabled')
                    ->label(__('notifications.enabled'))
                    ->required(),
                TextInput::make('edit_preference_message')
                    ->label(__('notifications.edit_pref_msg'))
                    ->hint(__('notifications.edit_pref_msg_hint'))
                    ->columnSpanFull(),
                Section::make(__('notifications.email_template'))
                    ->description(__('notifications.email_template_desc'))
                    ->columns(2)
                    ->columnSpanFull()
                    ->collapsible()
                    ->schema([
                        TextInput::make('subject')
                            ->label(__('notifications.subject'))
                            ->required()
                            ->maxLength(255),
                        Select::make('mail_enabled')
                            ->label(__('notifications.mail_enabled'))
                            ->options([
                                NotificationEnabledStatus::ChoiceOn->value => __('notifications.choice_on'),
                                NotificationEnabledStatus::ChoiceOff->value => __('notifications.choice_off'),
                                NotificationEnabledStatus::Force->value => __('notifications.force_on'),
                                NotificationEnabledStatus::Never->value => __('notifications.force_off'),
                            ])
                            ->required(),
                        MarkdownEditor::make('body')
                            ->hint(__('notifications.body_hint'))
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
                Section::make(__('notifications.in_app_notification'))
                    ->description(__('notifications.in_app_notification_desc'))
                    ->columns(2)
                    ->columnSpanFull()
                    ->collapsible()
                    ->schema([
                        TextInput::make('in_app_title')
                            ->label(__('notifications.in_app_title'))
                            ->required(fn (Get $get) => $get('in_app_enabled') !== NotificationEnabledStatus::Never->value)
                            ->disabled(fn (Get $get) => $get('in_app_enabled') === NotificationEnabledStatus::Never->value)
                            ->maxLength(255),
                        Select::make('in_app_enabled')
                            ->label(__('notifications.in_app_enabled'))
                            ->options([
                                NotificationEnabledStatus::ChoiceOn->value => __('notifications.choice_on'),
                                NotificationEnabledStatus::ChoiceOff->value => __('notifications.choice_off'),
                                NotificationEnabledStatus::Force->value => __('notifications.force_on'),
                                NotificationEnabledStatus::Never->value => __('notifications.force_off'),
                            ])
                            ->live()
                            ->required(),
                        TextInput::make('in_app_body')
                            ->label(__('notifications.in_app_body'))
                            ->required(fn (Get $get) => $get('in_app_enabled') !== NotificationEnabledStatus::Never->value)
                            ->disabled(fn (Get $get) => $get('in_app_enabled') === NotificationEnabledStatus::Never->value)
                            ->columnSpanFull(),
                        TextInput::make('in_app_url')
                            ->label(__('notifications.in_app_url'))
                            ->maxLength(255)
                            ->placeholder('{{ route("invoices.show", $invoice) }}')
                            ->hint(__('notifications.in_app_url_hint'))
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
                    ->label(__('notifications.subject'))
                    ->searchable(),
                IconColumn::make('enabled')
                    ->label(__('notifications.enabled'))
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
