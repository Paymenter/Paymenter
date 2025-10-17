<?php

namespace App\Livewire;

use App\Events\Properties\Updated as PropertiesUpdated;
use App\Models\CustomProperty;
use App\Services\CustomPropertyVisibilityService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ComponentWithProperties extends Component
{
    // Custom Properties which will be used to render the Inputs
    public Collection $custom_properties;

    public string $customPropertyMorphClass = '';

    // Values of the Custom Properties
    public array $properties = [];

    /**
     * Updates the properties of the model
     *
     * @param  Model|null  $model
     * @param  string  $morphClass
     */
    public function initializeProperties($model, $morphClass)
    {
        $this->customPropertyMorphClass = $morphClass;

        $this->properties = [];

        if ($model) {
            $this->properties = $model
                ->properties->mapWithKeys(function ($property) {
                    return [$property->key => $property->value];
                })
                ->toArray();
        }

        $this->refreshVisibleProperties();
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
     * @param  Model  $model
     * @param  array  $properties
     */
    public function updateProperties($model, $properties)
    {
        $properties = collect($properties)->map(function ($value, $key) use ($model) {
            $custom_property = $this->custom_properties->firstWhere('key', $key);

            if (! $custom_property) {
                return null;
            }

            if ($custom_property->non_editable && $model->properties->where('key', $key)->first()) {
                return null;
            }

            return [
                'key' => $key,
                'value' => $value,
                'model_id' => $model->id,
                'model_type' => $model->getMorphClass(),
                'name' => $custom_property->name,
                'custom_property_id' => $custom_property->id,
            ];
        })->filter()->toArray();

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

    protected function refreshVisibleProperties(): void
    {
        if (! isset($this->custom_properties)) {
            $this->custom_properties = collect();
        }

        if ($this->customPropertyMorphClass === '') {
            $this->custom_properties = collect();

            return;
        }

        $allProperties = CustomProperty::query()
            ->where('model', $this->customPropertyMorphClass)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        /** @var CustomPropertyVisibilityService $resolver */
        $resolver = app(CustomPropertyVisibilityService::class);

        $visible = $resolver->filter($allProperties, $this->properties)->values();
        $visibleKeys = $visible->pluck('key')->all();

        $existingKeys = $this->custom_properties instanceof Collection
            ? $this->custom_properties->pluck('key')->all()
            : [];

        if ($existingKeys !== $visibleKeys) {
            $this->custom_properties = $visible;
        } elseif ($this->custom_properties instanceof Collection) {
            $this->custom_properties = $this->custom_properties->map(function ($property) use ($visible) {
                $replacement = $visible->firstWhere('key', $property->key);

                return $replacement ?? $property;
            });
        }

        if ($this->properties !== []) {
            $currentValues = $this->properties;
            $this->properties = [];

            foreach ($visibleKeys as $key) {
                if (array_key_exists($key, $currentValues)) {
                    $this->properties[$key] = $currentValues[$key];
                }
            }
        }
    }

    public function updated($propertyName, $value): void
    {
        if (is_string($propertyName) && str_starts_with($propertyName, 'properties.')) {
            $this->refreshVisibleProperties();
        }
    }
}
