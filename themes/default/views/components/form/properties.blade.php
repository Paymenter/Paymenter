@props(['properties', 'custom_properties' => []])

@foreach ($custom_properties as $property)
    @php($wireKey = 'custom-property-' . ($property->id ?? $property->key))
    @php($allowedValues = is_array($property->allowed_values) ? $property->allowed_values : [])

    <div wire:key="{{ $wireKey }}">
        @switch($property->type)
            @case('date')
            @case('string')
            @case('number')
                <x-form.input
                    :type="$property->type"
                    name="properties.{{ $property->key }}"
                    :label="$property->name"
                    :required="$property->required"
                    wire:model.live="properties.{{ $property->key }}"
                    :value="$properties[$property->key] ?? ''"
                    :disabled="$property->non_editable && isset($properties[$property->key])"
                />
            @break

            @case('checkbox')
                <x-form.checkbox
                    name="properties.{{ $property->key }}"
                    :label="$property->name"
                    :required="$property->required"
                    wire:model.live="properties.{{ $property->key }}"
                    :checked="$properties[$property->key] ?? false"
                    :disabled="$property->non_editable && isset($properties[$property->key])"
                />
            @break

            @case('radio')
                @foreach ($allowedValues as $value)
                    @php($optionKey = $wireKey . '-' . md5((string) $value))

                    <div class="flex items-center" wire:key="{{ $optionKey }}">
                        <input
                            type="radio"
                            value="{{ $value }}"
                            name="properties.{{ $property->key }}"
                            @checked(($properties[$property->key] ?? null) === $value)
                            @required($property->required)
                            wire:model.live="properties.{{ $property->key }}"
                            :disabled="$property->non_editable && isset($properties[$property->key])"
                            class="form-radio size-4 text-primary rounded-full focus:ring-secondary hover:bg-secondary ring-offset-primary-800 focus:ring-2 bg-background-secondary border-neutral"
                        />
                        <label class="ml-2 text-sm text-primary-100" for="properties.{{ $property->key }}">{{ $value }}</label>
                    </div>
                @endforeach
            @break

            @case('select')
                <x-form.select
                    name="properties.{{ $property->key }}"
                    :label="$property->name"
                    wire:model.live="properties.{{ $property->key }}"
                    :required="$property->required"
                    :options="$allowedValues"
                    :selected="$properties[$property->key] ?? ''"
                    :disabled="$property->non_editable && isset($properties[$property->key])"
                />
            @break

            @case('text')
                <x-form.textarea
                    :type="$property->type"
                    name="properties.{{ $property->key }}"
                    :label="$property->name"
                    :required="$property->required"
                    wire:model.live="properties.{{ $property->key }}"
                    :disabled="$property->non_editable && isset($properties[$property->key])"
                >{{ $properties[$property->key] ?? '' }}</x-form.textarea>
            @break

            @default
        @endswitch
    </div>
@endforeach
