<?php

namespace App\Livewire;

use App\Events\Properties\Updated as PropertiesUpdated;
use Illuminate\Database\Eloquent\Collection;

class ComponentWithProperties extends Component
{
    // Custom Properties which will be used to render the Inputs
    public Collection $custom_properties;

    // Values of the Custom Properties
    public array $properties = [];

    /**
     * Updates the properties of the model
     *
     * @param  \Illuminate\Database\Eloquent\Model|null  $model
     * @param  string  $morphClass
     */
    public function initializeProperties($model, $morphClass)
    {
        $this->custom_properties = \App\Models\CustomProperty::where('model', $morphClass)->get();
        if ($model) {
            $this->properties = $model
                ->properties->mapWithKeys(function ($property) {
                    return [$property->key => $property->value];
                })
                ->toArray();
        }
    }

    public function getRulesForProperties(): array
    {
        return $this->custom_properties->mapWithKeys(function ($property) {
            return ["properties.$property->key" => ($property->required ? 'required|' : 'nullable|') . "$property->validation"];
        })->toArray();
    }

    /**
     * Returns the attributes of the properties to show in error messages
     */
    public function getAttributesForProperties(): array
    {
        return $this->custom_properties->mapWithKeys(function ($property) {
            return ["properties.$property->key" => $property->name];
        })->toArray();
    }

    /**
     * Updates the properties of the model
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  array  $properties
     */
    public function updateProperties($model, $properties)
    {
        $properties = collect($properties)->map(function ($value, $key) use ($model) {
            $custom_property = $this->custom_properties->where('key', $key)->first();

            return [
                'key' => $key,
                'value' => $value,
                'model_id' => $model->id,
                'model_type' => $model->getMorphClass(),
                'name' => $custom_property->name,
                'custom_property_id' => $custom_property->id,
            ];
        })->toArray();

        $model->properties()->upsert($properties, uniqueBy: [
            'key',
            'model_id',
            'model_type',
        ], update: [
            'name',
            'value',
            'model_id',
            'model_type',
            'custom_property_id',
        ]);

        event(new PropertiesUpdated($model, $properties));
    }
}
