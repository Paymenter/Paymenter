<?php

namespace App\Admin\Resources\NotificationTemplateResource\Pages;

use App\Admin\Resources\NotificationTemplateResource;
use App\Classes\Settings;
use App\Models\NotificationTemplate;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\SelectAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @property-read NotificationTemplate $record
 */
class EditNotificationTemplate extends EditRecord
{
    protected static string $resource = NotificationTemplateResource::class;

    public ?string $activeLocale = null;

    /**
     * @var list<string>
     */
    protected array $nonTranslatableFields = [
        'key',
        'enabled',
        'mail_enabled',
        'in_app_enabled',
        'cc',
        'bcc',
    ];

    public function mount(int|string $record): void
    {
        $this->activeLocale = $this->getDefaultLocale();

        parent::mount($record);
    }

    public function getDefaultLocale(): string
    {
        return (string) config('app.locale');
    }

    public function isEditingDefaultLocale(): bool
    {
        return $this->activeLocale === $this->getDefaultLocale();
    }

    public function updatedActiveLocale(?string $locale): void
    {
        if (!is_string($locale) || $locale === '') {
            $this->activeLocale = $this->getDefaultLocale();
        }

        if (!in_array($this->activeLocale, array_keys(Settings::getAllowedLanguageOptions()), true)) {
            $this->activeLocale = $this->getDefaultLocale();
        }

        $this->fillForm();
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $localeAttributes = $this->record->attributesForLocale(
            $this->activeLocale ?? $this->getDefaultLocale(),
            $this->getDefaultLocale()
        );

        return array_merge($data, $localeAttributes);
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        /** @var NotificationTemplate $record */
        $meta = collect($data)->only($this->nonTranslatableFields)->all();
        $translatable = collect($data)->only(NotificationTemplate::translatableFields())->all();

        $record->update($meta);

        if ($this->isEditingDefaultLocale()) {
            $record->update($translatable);

            return $record->refresh();
        }

        $subject = trim((string) ($translatable['subject'] ?? ''));
        $body = trim((string) ($translatable['body'] ?? ''));

        if ($subject === '' && $body === '') {
            $record->translations()->where('locale', $this->activeLocale)->delete();

            return $record->refresh();
        }

        $record->translations()->updateOrCreate(
            ['locale' => $this->activeLocale],
            $translatable
        );

        return $record->refresh();
    }

    protected function getHeaderActions(): array
    {
        return [
            SelectAction::make('activeLocale')
                ->options(fn (): array => Settings::getAllowedLanguageOptions()),
            Action::make('prefillFromDefault')
                ->label('Prefill from default language')
                ->icon('ri-file-copy-line')
                ->color('gray')
                ->visible(fn (): bool => !$this->isEditingDefaultLocale())
                ->requiresConfirmation()
                ->modalHeading('Prefill from default language')
                ->modalDescription('This copies the default language content into the form. Nothing is saved until you click Save.')
                ->action(function (): void {
                    $this->form->fill(array_merge(
                        $this->record->attributesToArray(),
                        $this->record->defaultLocaleAttributes(),
                    ));

                    Notification::make()
                        ->title('Form prefilled from default language')
                        ->success()
                        ->send();
                }),
            DeleteAction::make(),
        ];
    }

    protected function getSavedNotificationTitle(): ?string
    {
        $label = Settings::getAllowedLanguageOptions()[$this->activeLocale]
            ?? Settings::getAvailableLanguageOptions()[$this->activeLocale]
            ?? Str::upper((string) $this->activeLocale);

        return "Saved ({$label})";
    }
}
