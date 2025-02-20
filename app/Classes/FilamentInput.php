<?php

namespace App\Classes;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;

class FilamentInput
{
    /**
     * Convert array or object to Filament input
     *
     * @param  array|object  $setting
     * @return mixed
     */
    public static function convert($setting)
    {
        if (is_array($setting)) {
            $setting = (object) $setting;
        }

        switch ($setting->type) {
            case 'select':
                return Select::make($setting->name)
                    ->label($setting->label ?? $setting->name)
                    ->helperText($setting->description ?? '')
                    ->options(function () use ($setting) {
                        // Check if options are associative array or sequential array
                        if (array_is_list((array) $setting->options)) {
                            // If yes, then return array which has the keys same as the values
                            $options_with_keys = array_merge(...array_map(fn ($item) => [$item => $item], $setting->options));

                            return $options_with_keys;
                        } else {
                            return (array) $setting->options;
                        }
                    })
                    ->preload()
                    ->multiple($setting->multiple ?? false)
                    ->required($setting->required ?? false)
                    ->hint($setting->hint ?? '')
                    ->hintColor('primary')
                    ->live(condition: $setting->live ?? false)
                    ->default($setting->default ?? '')
                    ->suffix($setting->suffix ?? null)
                    ->prefix($setting->prefix ?? null)
                    ->disabled($setting->disabled ?? false)
                    ->rules($setting->validation ?? []);
                break;

            case 'tags':
                return TagsInput::make($setting->name)
                    ->label($setting->label ?? $setting->name)
                    ->placeholder($setting->placeholder ?? '')
                    ->required($setting->required ?? false)
                    ->disabled($setting->disabled ?? false)
                    ->hint($setting->hint ?? '')
                    ->hintColor('primary')
                    ->helperText($setting->description ?? '');
                break;

            case 'text':
                return TextInput::make($setting->name)
                    ->label($setting->label ?? $setting->name)
                    ->helperText($setting->description ?? '')
                    ->placeholder($setting->placeholder ?? $setting->default ?? '')
                    ->hint($setting->hint ?? '')
                    ->hintColor('primary')
                    ->required($setting->required ?? false)
                    ->live(condition: $setting->live ?? false)
                    ->default($setting->default ?? '')
                    ->suffix($setting->suffix ?? null)
                    ->prefix($setting->prefix ?? null)
                    ->disabled($setting->disabled ?? false)
                    ->rules($setting->validation ?? []);
                break;

            case 'textarea':
                return MarkdownEditor::make($setting->name)
                    ->label($setting->label ?? $setting->name)
                    ->helperText($setting->description ?? '')
                    ->placeholder($setting->placeholder ?? $setting->default ?? '')
                    ->hint($setting->hint ?? '')
                    ->hintColor('primary')
                    ->required($setting->required ?? false)
                    ->live(condition: $setting->live ?? false)
                    ->default($setting->default ?? '')
                    ->disableAllToolbarButtons($setting->disable_toolbar ?? false)
                    ->rules($setting->validation ?? [])
                    ->disabled($setting->disabled ?? false);
                break;
            case 'markdown':
                return MarkdownEditor::make($setting->name)
                    ->label($setting->label ?? $setting->name)
                    ->helperText($setting->description ?? '')
                    ->placeholder($setting->placeholder ?? $setting->default ?? '')
                    ->hint($setting->hint ?? '')
                    ->hintColor('primary')
                    ->required($setting->required ?? false)
                    ->live(condition: $setting->live ?? false)
                    ->default($setting->default ?? '')
                    ->disableAllToolbarButtons($setting->disable_toolbar ?? false)
                    ->rules($setting->validation ?? [])
                    ->disabled($setting->disabled ?? false);
                break;
            case 'password':
                return TextInput::make($setting->name)
                    ->label($setting->label ?? $setting->name)
                    ->helperText($setting->description ?? '')
                    ->placeholder($setting->placeholder ?? $setting->default ?? '')
                    ->hint($setting->hint ?? '')
                    ->hintColor('primary')
                    ->required($setting->required ?? false)
                    ->password()
                    ->revealable()
                    ->live(condition: $setting->live ?? false)
                    ->default($setting->default ?? '')
                    ->suffix($setting->suffix ?? null)
                    ->prefix($setting->prefix ?? null)
                    ->disabled($setting->disabled ?? false)
                    ->rules($setting->validation ?? []);
                break;
            case 'email':
                return TextInput::make($setting->name)
                    ->label($setting->label ?? $setting->name)
                    ->helperText($setting->description ?? '')
                    ->placeholder($setting->placeholder ?? $setting->default ?? '')
                    ->hint($setting->hint ?? '')
                    ->hintColor('primary')
                    ->required($setting->required ?? false)
                    ->email()
                    ->live(condition: $setting->live ?? false)
                    ->default($setting->default ?? '')
                    ->suffix($setting->suffix ?? null)
                    ->prefix($setting->prefix ?? null)
                    ->disabled($setting->disabled ?? false)
                    ->rules($setting->validation ?? []);
                break;
            case 'number':
                return TextInput::make($setting->name)
                    ->label($setting->label ?? $setting->name)
                    ->helperText($setting->description ?? '')
                    ->placeholder($setting->placeholder ?? $setting->default ?? '')
                    ->hint($setting->hint ?? '')
                    ->hintColor('primary')
                    ->required($setting->required ?? false)
                    ->numeric()
                    ->minValue($setting->min_value ?? null)
                    ->maxValue($setting->max_value ?? null)
                    ->live(condition: $setting->live ?? false)
                    ->default($setting->default ?? '')
                    ->suffix($setting->suffix ?? null)
                    ->prefix($setting->prefix ?? null)
                    ->disabled($setting->disabled ?? false)
                    ->rules($setting->validation ?? []);

                break;
            case 'color':
                $color = ColorPicker::make($setting->name)
                    ->label($setting->label ?? $setting->name)
                    ->helperText($setting->description ?? '')
                    ->placeholder($setting->placeholder ?? $setting->default ?? '')
                    ->hint($setting->hint ?? '')
                    ->hintColor('primary')
                    ->required($setting->required ?? false)
                    ->regex('/^hsl\(\s*(\d+)\s*,\s*(\d*(?:\.\d+)?%)\s*,\s*(\d*(?:\.\d+)?%)\)$/')
                    ->live(condition: $setting->live ?? false)
                    ->default($setting->default ?? '')
                    ->suffix($setting->suffix ?? null)
                    ->prefix($setting->prefix ?? null)
                    ->disabled($setting->disabled ?? false)
                    ->rules($setting->validation ?? []);
                if (isset($setting->color_mode)) {
                    $color->{$setting->color_mode}();
                } else {
                    $color->hsl();
                }

                return $color;
                break;
            case 'file':
                $input = FileUpload::make($setting->name)
                    ->label($setting->label ?? $setting->name)
                    ->helperText($setting->description ?? '')
                    ->hint($setting->hint ?? '')
                    ->hintColor('primary')
                    ->required($setting->required ?? false)
                    ->acceptedFileTypes($setting->accept ?? [])
                    ->live(condition: $setting->live ?? false)
                    ->default($setting->default ?? '')
                    ->disabled($setting->disabled ?? false)
                    ->rules($setting->validation ?? []);

                if (isset($setting->file_name)) {
                    $input->getUploadedFileNameForStorageUsing(
                        fn (): string => (string) $setting->file_name,
                    );
                }

                return $input;

                break;

            case 'checkbox':
                return Checkbox::make($setting->name)
                    ->label($setting->label ?? $setting->name)
                    ->helperText($setting->description ?? '')
                    ->required($setting->required ?? false)
                    ->hint($setting->hint ?? '')
                    ->hintColor('primary')
                    ->live(condition: $setting->live ?? false)
                    ->default($setting->default ?? '')
                    ->disabled($setting->disabled ?? false)
                    ->rules($setting->validation ?? []);
                break;

            case 'placeholder':
                return Placeholder::make($setting->name)
                    ->content($setting->label ?? null)
                    ->helperText($setting->description ?? '')
                    ->hint($setting->hint ?? '')
                    ->hintColor('primary');
                break;

            default:
        }
    }
}
