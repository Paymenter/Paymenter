<x-app-layout>
    <x-center>
        <div class="mt-6">
    @auth
            {{-- Example user property inputs in client side --}}
            @php
                $properties = \App\Models\UserProperty::whereNot('admin_only', true)->get();
                $property_values = Auth::user()->properties;
                dump($properties->toArray(), $property_values);
            @endphp

            @foreach ($properties as $property)
                @switch($property->type)
                    @case('date')
                    @case('text')

                    @case('number')
                        <x-form.input :type="$property->type" :name="$property->key" :label="$property->name" :required="$property->required"
                            wire:model="{{ $property->key }}" :value="$property_values[$property->key] ?? ''" />
                    @break

                    @case('checkbox')
                        <x-form.checkbox :name="$property->key" :label="$property->name" :required="$property->required" wire:model="{{ $property->key }}"
                            :checked="$property_values[$property->key] ?? false" />
                    @break

                    @case('radio')
                        @foreach ($property->allowed_values as $value)
                            <div class="flex items-center {{ $divClass ?? '' }}">
                                <input type="radio" value="{{ $value }}" name="{{ $property->key }}" type="radio"
                                    @checked($property_values[$property->key] === $value ?? false) label="{{ $value }}" @required($property->required)
                                    wire:model="{{ $property->key }}"
                                    class="form-radio w-4 h-4 text-primary rounded-full focus:ring-secondary hover:bg-secondary ring-offset-primary-800 focus:ring-2 bg-gray-700 border-gray-600" />
                                <label class="ml-2 text-sm text-primary-100"
                                    for="{{ $property->key }}">{{ $value }}</label>
                            </div>
                        @endforeach
                    @break

                    @case('select')
                        <x-form.select :name="$property->key" :label="$property->name" wire:model="{{ $property->key }}" :required="$property->required"
                            :options="$property->allowed_values" :selected="$property_values[$property->key] ?? ''" />
                    @break

                    @default
                @endswitch
            @endforeach
            @endauth
        </div>

    </x-center>

</x-app-layout>
