<?php

namespace App\Models;

use App\Enums\NotificationEnabledStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class NotificationTemplate extends Model implements Auditable
{
    use HasFactory, Traits\Auditable;

    /**
     * @var list<string>
     */
    protected static array $translatableFields = [
        'subject',
        'body',
        'in_app_title',
        'in_app_body',
        'in_app_url',
        'edit_preference_message',
    ];

    protected $fillable = [
        'key',
        'name',
        'subject',
        'enabled',
        'body',
        'cc',
        'bcc',
        'mail_enabled',
        'in_app_enabled',
        'in_app_title',
        'in_app_body',
        'in_app_url',
        'edit_preference_message',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'cc' => 'array',
        'bcc' => 'array',
        'mail_enabled' => NotificationEnabledStatus::class,
        'in_app_enabled' => NotificationEnabledStatus::class,
    ];

    /**
     * @return HasMany<NotificationPreference, $this>
     */
    public function preferences()
    {
        return $this->hasMany(NotificationPreference::class);
    }

    /**
     * @return HasMany<NotificationTemplateTranslation, $this>
     */
    public function translations(): HasMany
    {
        return $this->hasMany(NotificationTemplateTranslation::class);
    }

    /**
     * Return a template instance with translated content for the given locale.
     *
     * Fallback order: exact locale → app locale → base template columns.
     */
    public function resolveForLocale(?string $locale = null): self
    {
        $locale = $locale ?: (string) config('app.locale');
        $fallbackLocale = (string) config('app.locale');

        $translation = $this->findTranslation($locale);

        if (!$translation && $locale !== $fallbackLocale) {
            $translation = $this->findTranslation($fallbackLocale);
        }

        if (!$translation) {
            return $this;
        }

        $resolved = $this->newInstance([], true);
        $resolved->exists = true;
        $resolved->setRawAttributes($this->getAttributes());
        $resolved->setRelations($this->relations);

        foreach (self::$translatableFields as $field) {
            $value = $translation->{$field};

            if ($value !== null) {
                $resolved->setAttribute($field, $value);
            }
        }

        return $resolved;
    }

    protected function findTranslation(string $locale): ?NotificationTemplateTranslation
    {
        if ($this->relationLoaded('translations')) {
            return $this->translations->firstWhere('locale', $locale);
        }

        return $this->translations()->where('locale', $locale)->first();
    }

    /**
     * @return list<string>
     */
    public static function translatableFields(): array
    {
        return self::$translatableFields;
    }

    /**
     * @return array<string, mixed>
     */
    public function defaultLocaleAttributes(): array
    {
        $attributes = [];

        foreach (self::$translatableFields as $field) {
            $attributes[$field] = $this->getAttribute($field);
        }

        return $attributes;
    }

    /**
     * @return array<string, mixed>
     */
    public function attributesForLocale(string $locale, string $defaultLocale): array
    {
        if ($locale === $defaultLocale) {
            return $this->defaultLocaleAttributes();
        }

        $translation = $this->findTranslation($locale);

        if (!$translation) {
            $empty = [];
            foreach (self::$translatableFields as $field) {
                $empty[$field] = null;
            }

            return $empty;
        }

        $attributes = [];
        foreach (self::$translatableFields as $field) {
            $attributes[$field] = $translation->{$field};
        }

        return $attributes;
    }

    /**
     * Locales that have content for this template (default language + stored translations).
     *
     * @return list<string>
     */
    public function availableLocales(): array
    {
        $defaultLocale = (string) config('app.locale');

        $translationLocales = $this->relationLoaded('translations')
            ? $this->translations->pluck('locale')->all()
            : $this->translations()->pluck('locale')->all();

        $others = array_values(array_filter(
            $translationLocales,
            static fn (string $locale): bool => $locale !== $defaultLocale
        ));
        sort($others, SORT_STRING);

        return [$defaultLocale, ...$others];
    }

    public function isEmailUserControllable()
    {
        return in_array($this->mail_enabled, [NotificationEnabledStatus::ChoiceOn, NotificationEnabledStatus::ChoiceOff]);
    }

    public function isInAppUserControllable()
    {
        return in_array($this->in_app_enabled, [NotificationEnabledStatus::ChoiceOn, NotificationEnabledStatus::ChoiceOff]);
    }

    // Check if user has enabled this notification for email
    public function isEnabledForPreference(?NotificationPreference $preference = null, $type = 'mail')
    {
        $type = $type === 'app' ? 'in_app_enabled' : 'mail_enabled';
        if ($this->{$type} === NotificationEnabledStatus::Force) {
            return true;
        }
        if ($this->{$type} === NotificationEnabledStatus::Never) {
            return false;
        }

        if ($preference) {
            return $preference->{$type};
        }

        // Return true if choice_on, false if choice_off
        return $this->{$type} === NotificationEnabledStatus::ChoiceOn;
    }
}
