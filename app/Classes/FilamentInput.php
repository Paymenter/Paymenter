<?php

namespace App\Classes;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class FilamentInput
{
    /**
     * Convert array or object to Filament input
     * 
     * @param array|object $setting
     * @return mixed
     */
    public static function convert($setting)
    {
        if (is_array($setting)) {
            $setting = (object) $setting;
        }
        switch ($setting->type) {
            case ('select'):
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
                    ->searchable()
                    ->required($setting->required ?? false)
                    ->hint($setting->hint ?? '')
                    ->hintColor('primary')
                    ->live(condition: $setting->live ?? false)
                    ->default($setting->default ?? '')
                    ->suffix($setting->suffix ?? null)
                    ->prefix($setting->prefix ?? null)
                    ->rules($setting->validation ?? []);
                break;

            case ('text'):
                return TextInput::make($setting->name)
                    ->label($setting->label ?? $setting->name)
                    ->helperText($setting->description ?? '')
                    ->placeholder($setting->default ?? "")
                    ->hint($setting->hint ?? '')
                    ->hintColor('primary')
                    ->required($setting->required ?? false)
                    ->live(condition: $setting->live ?? false)
                    ->default($setting->default ?? '')
                    ->suffix($setting->suffix ?? null)
                    ->prefix($setting->prefix ?? null)
                    ->rules($setting->validation ?? []);
                break;
            case ('password'):
                return TextInput::make($setting->name)
                    ->label($setting->label ?? $setting->name)
                    ->helperText($setting->description ?? '')
                    ->placeholder($setting->default ?? "")
                    ->required($setting->required ?? false)
                    ->password()
                    ->revealable()
                    ->live(condition: $setting->live ?? false)
                    ->default($setting->default ?? '')
                    ->suffix($setting->suffix ?? null)
                    ->prefix($setting->prefix ?? null)
                    ->rules($setting->validation ?? []);
                break;
            case ('email'):
                return TextInput::make($setting->name)
                    ->label($setting->label ?? $setting->name)
                    ->helperText($setting->description ?? '')
                    ->placeholder($setting->default ?? "")
                    ->required($setting->required ?? false)
                    ->email()
                    ->live(condition: $setting->live ?? false)
                    ->default($setting->default ?? '')
                    ->suffix($setting->suffix ?? null)
                    ->prefix($setting->prefix ?? null)
                    ->rules($setting->validation ?? []);
                break;
            case ('number'):
                return TextInput::make($setting->name)
                    ->label($setting->label ?? $setting->name)
                    ->helperText($setting->description ?? '')
                    ->placeholder($setting->default ?? "")
                    ->required($setting->required ?? false)
                    ->numeric()
                    ->minValue($setting->min_value ?? null)
                    ->maxValue($setting->max_value ?? null)
                    ->live(condition: $setting->live ?? false)
                    ->default($setting->default ?? '')
                    ->suffix($setting->suffix ?? null)
                    ->prefix($setting->prefix ?? null)
                    ->rules($setting->validation ?? []);

                break;
            case ('color'):
                return ColorPicker::make($setting->name)
                    ->label($setting->label ?? $setting->name)
                    ->helperText($setting->description ?? '')
                    ->placeholder($setting->default ?? "")
                    ->required($setting->required ?? false)
                    ->hexColor()
                    ->live(condition: $setting->live ?? false)
                    ->default($setting->default ?? '')
                    ->suffix($setting->suffix ?? null)
                    ->prefix($setting->prefix ?? null)
                    ->rules($setting->validation ?? []);
                break;
            case ('file'):
                $input = FileUpload::make($setting->name)
                    ->label($setting->label ?? $setting->name)
                    ->helperText($setting->description ?? '')
                    ->required($setting->required ?? false)
                    ->acceptedFileTypes($setting->accept)
                    ->live(condition: $setting->live ?? false)
                    ->default($setting->default ?? '')
                    ->suffix($setting->suffix ?? null)
                    ->prefix($setting->prefix ?? null)
                    ->rules($setting->validation ?? []);

                if (isset($setting->file_name)) {
                    $input->getUploadedFileNameForStorageUsing(
                        fn (): string => (string) $setting->file_name,
                    );
                }
                return $input;

                break;

            case ('checkbox'):
                return Checkbox::make($setting->name)
                    ->label($setting->label ?? $setting->name)
                    ->helperText($setting->description ?? '')
                    ->required($setting->required ?? false)
                    ->live(condition: $setting->live ?? false)
                    ->default($setting->default ?? '')
                    ->rules($setting->validation ?? []);
                break;

            default;
        }
    }
}
