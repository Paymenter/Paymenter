<?php

namespace App\Classes;

use Exception;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Spatie\Color\Factory as ColorFactory;

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
        // If its already a filament component, return it
        if (is_object($setting) && method_exists($setting, 'getName')) {
            return $setting;
        }
        if (is_array($setting)) {
            $setting = (object) $setting;
        }

        switch ($setting->type) {
            case 'select':
                return Select::make($setting->name)
                    ->label($setting->label ?? $setting->name)
                    ->helperText($setting->description ?? null)
                    ->options(function () use ($setting) {
                        /* Possiblities:
                            1. ['value1', 'value2', 'value3']
                            2. ['value1' => 'label1', 'value2' => 'label2', 'value3' => 'label3']
                            3. [[
                                    'value' => 'value1',
                                    'label' => 'label1',
                                ], [
                                    'value' => 'value2',
                                    'label' => 'label2',
                                ]]
                        */
                        if (isset($setting->options)) {
                            if (is_array($setting->options)) {
                                $options = [];
                                // Check if the keys are explicitly set or sequential
                                $keys = array_keys($setting->options);
                                $isSequential = $keys === range(0, count($keys) - 1);

                                foreach ($setting->options as $key => $value) {
                                    // Explicitly set keys (e.g., ['key1' => 'value1', 'key2' => 'value2'])
                                    if (is_array($value)) {
                                        $options[$value['value']] = $value['label'];
                                    } else {
                                        if ($isSequential) {
                                            // Sequential keys (e.g., [0 => 'value1', 1 => 'value2'])
                                            $options[$value] = $value;
                                        } else {
                                            $options[$key] = $value;
                                        }
                                    }
                                }

                                return $options;
                            } else {
                                return (array) $setting->options;
                            }
                        }

                        return [];
                    })
                    ->preload()
                    ->multiple($setting->multiple ?? false)
                    ->required($setting->required ?? false)
                    ->hint($setting->hint ?? null)
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
                    ->hint($setting->hint ?? null)
                    ->hintColor('primary')
                    ->rules($setting->validation ?? [])
                    ->nestedRecursiveRules($setting->nested_validation ?? [])
                    ->helperText($setting->description ?? null);
                break;

            case 'text':
                return TextInput::make($setting->name)
                    ->label($setting->label ?? $setting->name)
                    ->helperText($setting->description ?? null)
                    ->placeholder($setting->placeholder ?? $setting->default ?? '')
                    ->hint($setting->hint ?? null)
                    ->hintColor('primary')
                    ->required($setting->required ?? false)
                    ->live(condition: $setting->live ?? false)
                    ->default($setting->default ?? '')
                    ->suffix($setting->suffix ?? null)
                    ->prefix($setting->prefix ?? null)
                    ->disabled($setting->disabled ?? false)
                    ->rules($setting->validation ?? []);
                break;

            case 'time':
                return TimePicker::make($setting->name)
                    ->label($setting->label ?? $setting->name)
                    ->helperText($setting->description ?? null)
                    ->placeholder($setting->placeholder ?? $setting->default ?? '')
                    ->hint($setting->hint ?? null)
                    ->hintColor('primary')
                    ->required($setting->required ?? false)
                    ->live(condition: $setting->live ?? false)
                    ->default($setting->default ?? null)
                    ->suffix($setting->suffix ?? null)
                    ->prefix($setting->prefix ?? null)
                    ->disabled($setting->disabled ?? false)
                    ->rules($setting->validation ?? [])
                    ->seconds($setting->seconds ?? false);
                break;

            case 'textarea':
                return Textarea::make($setting->name)
                    ->label($setting->label ?? $setting->name)
                    ->helperText($setting->description ?? null)
                    ->placeholder($setting->placeholder ?? $setting->default ?? '')
                    ->hint($setting->hint ?? null)
                    ->hintColor('primary')
                    ->required($setting->required ?? false)
                    ->live(condition: $setting->live ?? false)
                    ->default($setting->default ?? '')
                    ->rules($setting->validation ?? [])
                    ->disabled($setting->disabled ?? false);
                break;

            case 'markdown':
                return MarkdownEditor::make($setting->name)
                    ->label($setting->label ?? $setting->name)
                    ->helperText($setting->description ?? null)
                    ->placeholder($setting->placeholder ?? $setting->default ?? '')
                    ->hint($setting->hint ?? null)
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
                    ->helperText($setting->description ?? null)
                    ->placeholder($setting->placeholder ?? $setting->default ?? '')
                    ->hint($setting->hint ?? null)
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
                    ->helperText($setting->description ?? null)
                    ->placeholder($setting->placeholder ?? $setting->default ?? '')
                    ->hint($setting->hint ?? null)
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
                    ->helperText($setting->description ?? null)
                    ->placeholder($setting->placeholder ?? $setting->default ?? '')
                    ->hint($setting->hint ?? null)
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
                $mode = $setting->color_mode ?? 'hsl';
                $color = ColorPicker::make($setting->name)
                    ->label($setting->label ?? $setting->name)
                    ->helperText($setting->description ?? null)
                    ->placeholder($setting->placeholder ?? $setting->default ?? '')
                    ->hint($setting->hint ?? null)
                    ->hintColor('primary')
                    ->required($setting->required ?? false)
                    ->live(condition: $setting->live ?? true)
                    ->default($setting->default ?? '')
                    ->suffix($setting->suffix ?? null)
                    ->prefix($setting->prefix ?? null)
                    ->disabled($setting->disabled ?? false)
                    ->rules($setting->validation ?? [])
                    ->rules(function () {
                        return function ($attribute, $value, $fail) {
                            try {
                                ColorFactory::fromString(trim($value));
                            } catch (Exception $e) {
                                $fail('The :attribute must be a valid color.');
                            }
                        };
                    })
                    ->afterStateUpdated(function ($state, callable $set) use ($setting, $mode) {
                        try {
                            $set($setting->name, preg_replace('/,\s*/', ', ', ColorFactory::fromString(trim($state))->{'to' . ucfirst($mode)}()->__toString()));
                        } catch (Exception $e) {
                        }
                    });
                $color->$mode();

                return $color;
                break;
            case 'file':
                $input = FileUpload::make($setting->name)
                    ->label($setting->label ?? $setting->name)
                    ->helperText($setting->description ?? null)
                    ->hint($setting->hint ?? null)
                    ->hintColor('primary')
                    ->required($setting->required ?? false)
                    ->acceptedFileTypes($setting->accept ?? [])
                    ->live(condition: $setting->live ?? false)
                    ->default($setting->default ?? '')
                    ->disk($setting->disk ?? 'public')
                    ->preserveFilenames($setting->preserve_filenames ?? true)
                    ->disabled($setting->disabled ?? false)
                    ->visibility($setting->visibility ?? 'private')
                    ->downloadable()
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
                    ->helperText($setting->description ?? null)
                    ->required($setting->required ?? false)
                    ->hint($setting->hint ?? null)
                    ->hintColor('primary')
                    ->live(condition: $setting->live ?? false)
                    ->default($setting->default ?? '')
                    ->disabled($setting->disabled ?? false)
                    ->rules($setting->validation ?? []);
                break;

            case 'placeholder':
                return Placeholder::make($setting->name)
                    ->content($setting->label ?? null)
                    ->helperText($setting->description ?? null)
                    ->hint($setting->hint ?? null)
                    ->hintColor('primary');
                break;

            default:
                throw new Exception("Unknown input type: {$setting->type}");
        }
    }
}
