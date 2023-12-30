@if ($type == 'checkbox')
    <div @isset($class) class="{{ $class }}" @endisset>
        <div class="flex items-start">
            <div class="flex items-center h-5">
                <input id="{{ $id ?? $name }}" type="checkbox"
                    @isset($value) value={{ $value }} @endisset
                    @isset($checked) {{ $checked ? 'checked' : '' }} @endisset
                    name={{ $name }}
                    autocomplete="@isset($autocomplete) {{ $autocomplete }} @else {{ $type }} @endisset"
                    @isset($disabled) {{ $disabled ? 'disabled' : '' }} @endisset
                    {{ $attributes->except('class') }}
                    class="w-4 h-4 border border-secondary-300 rounded bg-secondary-200 text-primary-400 focus:ring-3 focus:ring-primary-300 focus:ring-offset-2 ring-offset-secondary-50 dark:ring-offset-secondary-100"
                    @isset($required) {{ $required ? 'required' : '' }} @endisset>
            </div>
            <label for="{{ $id ?? $name }}"
                class="ml-2 text-sm font-medium text-secondary-700">{!! $label !!}</label>
        </div>
        @error($name)
            <label for="{{ $id ?? $name }}" class="text-sm text-danger-300">{{ $message }}</label>
        @enderror
    </div>
@elseif($type == 'color')
    <div @isset($class) class={{ $class }} @endisset>
        {{ $slot }}
        @isset($label)
            <label for="{{ $id ?? $name }}" class="text-sm text-secondary-600">{!! $label !!}</label>
        @endisset
        <div class="relative">
            @isset($icon)
                <div class="absolute pointer-events-none top-0 left-0 py-2 px-4">
                    <i class="{{ $icon }}"></i>
                </div>
            @endisset
            <input type={{ $type }}
                @isset($placeholder) placeholder={{ $placeholder }} @endisset
                name={{ $name }}
                autocomplete="@isset($autocomplete) {{ $autocomplete }} @else {{ $type }} @endisset"
                @isset($value) value={{ $value }} @else value="{{ old($name) }}" @endisset
                id={{ $id ?? $name }}
                {{ $attributes->except('class') }}
                @isset($required) {{ $required ? 'required' : '' }} @endisset
                class="bg-secondary-200 text-secondary-800 font-medium rounded-md placeholder-secondary-500 outline-none w-full border focus:ring-2 focus:ring-offset-2 ring-offset-secondary-50 dark:ring-offset-secondary-100 duration-300
            @error($name) border-danger-300 focus:border-danger-400 focus:ring-danger-300 @else border-secondary-300 focus:border-secondary-400 focus:ring-primary-400 @enderror">
        </div>
        @error($name)
            <label for="{{ $id ?? $name }}" class="text-sm text-danger-300">{{ $message }}</label>
        @enderror
    </div>
@elseif($type == 'select')
    <div @isset($class) class="{{ $class }}" @endisset>
        @isset($label)
            <label for="{{ $id ?? $name }}" class="text-sm text-secondary-600">{!! $label !!}</label>
        @endisset
        <div class="relative">
            @isset($icon)
                <div class="absolute pointer-events-none top-0 left-0 py-2 px-4">
                    <i class="{{ $icon }}"></i>
                </div>
            @endisset
            <select {{ $attributes->except('class') }} id={{ $id ?? $name }}
                @isset($required) {{ $required ? 'required' : '' }} @endisset
                class="bg-secondary-200 text-secondary-800 font-medium rounded-md placeholder-secondary-500 outline-none w-full border focus:ring-2 focus:ring-offset-2 ring-offset-secondary-50 dark:ring-offset-secondary-100 duration-300
            @error($name) border-danger-300 focus:border-danger-400 focus:ring-danger-300 @else border-secondary-300 focus:border-secondary-400 focus:ring-primary-400 @enderror
            @isset($icon) pl-10 pr-4 @else px-4 @endisset">
                {{ $slot }}
            </select>
        </div>
        @error($name)
            <label for="{{ $id ?? $name }}" class="text-sm text-danger-300">{{ $message }}</label>
        @enderror
    </div>
@elseif($type == 'searchselect')
    <div @isset($class) class="{{ $class }}" @endisset>
        @isset($label)
            <label for="{{ $id ?? $name }}" class="text-sm text-secondary-600">{!! $label !!}</label>
        @endisset
        <div class="relative h-fit">
            @isset($icon)
                <div class="absolute pointer-events-none top-0 left-0 py-2 px-4">
                    <i class="{{ $icon }}"></i>
                </div>
            @endisset
            <input type="search" {{ $attributes->except('class') }} id={{ $id ?? $name }}
                @isset($required) {{ $required ? 'required' : '' }} @endisset
                class="bg-secondary-200 text-secondary-800 font-medium rounded-md placeholder-secondary-500 outline-none w-full border focus:ring-2 focus:ring-offset-2 ring-offset-secondary-50 dark:ring-offset-secondary-100 duration-300
                @error($name) border-danger-300 focus:border-danger-400 focus:ring-danger-300 @else border-secondary-300 focus:border-secondary-400 focus:ring-primary-400 @enderror
                @isset($icon) pl-10 pr-4 @else px-4 @endisset" autocomplete="off">
        
            <div id="{{$id ?? $name }}-list" class="mt-1 absolute hidden z-10 w-full bg-secondary-200 text-secondary-800 font-medium rounded-md placeholder-secondary-500 outline-none border max-h-[100px] overflow-y-scroll focus:ring-2 focus:ring-offset-2 ring-offset-secondary-50 dark:ring-offset-secondary-100 duration-300
                @error($name) border-danger-300 focus:border-danger-400 focus:ring-danger-300 @else border-secondary-300 focus:border-secondary-400 focus:ring-primary-400 @enderror
                @isset($icon) pl-10 pr-4 @else px-4 @endisset">
                    {{ $slot }}
                </div>
            </div> 
        <input id="{{ $id ?? $name }}-hidden" class="hidden" value="{{ old($name) }}" name="{{ $name }}" />
        @error($name)
            <label for="{{ $id ?? $name }}" class="text-sm text-danger-300">{{ $message }}</label>
        @enderror
    </div>
    <script>
        // Default list
        var list = [];
        var options = document.getElementById("{{ $id ?? $name }}-list").getElementsByTagName("option");
        for (var i = 0; i < options.length; i++) {
            list.push(options[i]);
        }
        document.getElementById("{{ $id ?? $name }}").addEventListener("keyup", function(event) {
            filterFunction();
        });

        document.getElementById("{{ $id ?? $name }}").addEventListener("click", function(event) {
            filterFunction();
        });

        document.getElementById("{{ $id ?? $name }}").addEventListener("focus", function(event) {
            filterFunction();
        });

        document.addEventListener("click", function(event) {
            var div = document.getElementById("{{ $id ?? $name }}-list");
            if (event.target != document.getElementById("{{ $id ?? $name }}") && event.target != div) {
                div.classList.add("hidden");
            }
        });

        // If the user clicks on the list, select the option
        document.getElementById("{{ $id ?? $name }}-list").addEventListener("click", function(event) {
            var div = document.getElementById("{{ $id ?? $name }}-list");
            if (event.target != div) {
                // Remove first spaces
                document.getElementById("{{ $id ?? $name }}").value = event.target.textContent.trim();
                document.getElementById("{{ $id ?? $name }}-hidden").value = event.target.value;
                div.classList.add("hidden");
            }
        });

        function filterFunction() {
            // Filter the list
            var input, filter, ul, li, a, i, txtValue;
            input = document.getElementById("{{ $id ?? $name }}");
            filter = input.value.toUpperCase();
            div = document.getElementById("{{ $id ?? $name }}-list");
            
            var newlist = [];
            for (var i = 0; i < list.length; i++) {
                // If its empty, add all
                if (input.value.length == 0) {
                    newlist.push(list[i]);
                    continue;
                }
                txtValue = list[i].textContent || list[i].innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1 && list[i].disabled == false) {
                    newlist.push(list[i]);
                }
            }

            // Clear the list
            while (div.firstChild) {
                div.removeChild(div.firstChild);
            }

            // Add the new list
            for (var i = 0; i < newlist.length; i++) {
                div.appendChild(newlist[i]);
            }

            // Show the lis
            div.classList.remove("hidden");
        }

        
    </script>
@elseif($type == 'textarea')
    <div @isset($class) class="{{ $class }}" @endisset>
        @isset($label)
            <label for="{{ $id ?? $name }}" class="text-sm text-secondary-600">{!! $label !!}</label>
        @endisset
        <div class="relative">
            @isset($icon)
                <div class="absolute pointer-events-none top-0 left-0 py-2 px-4">
                    <i class="{{ $icon }}"></i>
                </div>
            @endisset
            <textarea {{ $attributes->except('class') }} id={{ $id ?? $name }}
                @isset($required) {{ $required ? 'required' : '' }} @endisset
                class="bg-secondary-200 text-secondary-800 font-medium rounded-md placeholder-secondary-500 outline-none w-full border focus:ring-2 focus:ring-offset-2 ring-offset-secondary-50 dark:ring-offset-secondary-100 duration-300
            @error($name) border-danger-300 focus:border-danger-400 focus:ring-danger-300 @else border-secondary-300 focus:border-secondary-400 focus:ring-primary-400 @enderror">{{ $value ?? $slot }}</textarea>
        </div>
        @error($name)
            <label for="{{ $id ?? $name }}" class="text-sm text-danger-300">{{ $message }}</label>
        @enderror
    </div>
@elseif($type == 'file')
    <div @isset($class) class="{{ $class }}" @endisset>
        {{ $slot }}
        @isset($label)
            <label for="{{ $id ?? $name }}" class="text-sm text-secondary-600">{{ $label }}</label>
        @endisset
        <div class="relative">
            @isset($icon)
                <div class="absolute pointer-events-none top-0 left-0 py-2 px-4">
                    <i class="{{ $icon }}"></i>
                </div>
            @endisset
            <input type={{ $type }} {{ $attributes->except('class') }}
                @isset($value) value="{{ $value }}" @else value="{{ old($name) }}" @endisset
                id={{ $id ?? $name }}
                @isset($required) {{ $required ? 'required' : '' }} @endisset
                class="bg-secondary-200 text-secondary-800 font-medium rounded-md placeholder-secondary-500 outline-none w-full border focus:ring-2 focus:ring-offset-2 ring-offset-secondary-50 dark:ring-offset-secondary-100 duration-300 disabled:opacity-50
            @isset($icon) pl-10 pr-4 @else px-4 @endisset
            @error($name) border-danger-300 focus:border-danger-400 focus:ring-danger-300 @else border-secondary-300 focus:border-secondary-400 focus:ring-primary-400 @enderror">
        </div>
        @error($name)
            <label for={{ $id ?? $name }} class="text-sm text-danger-300">{{ $message }}</label>
        @enderror
    </div>
@else 
    <div @isset($class) class="{{ $class }}" @endisset>
        {{ $slot }}
        @isset($label)
            <label for="{{ $id ?? $name }}" class="text-sm text-secondary-600">{{ $label }}</label>
        @endisset
        <div class="relative">
            @isset($icon)
                <div class="absolute pointer-events-none top-0 left-0 py-2 px-4">
                    <i class="{{ $icon }}"></i>
                </div>
            @endisset
            <input type={{ $type }} {{ $attributes->except('class') }}
                @isset($value) value="{{ $value }}" @else value="{{ old($name) }}" @endisset
                id={{ $id ?? $name }}
                @isset($required) {{ $required ? 'required' : '' }} @endisset
                class="py-2 bg-secondary-200 text-secondary-800 font-medium rounded-md placeholder-secondary-500 outline-none w-full border focus:ring-2 focus:ring-offset-2 ring-offset-secondary-50 dark:ring-offset-secondary-100 duration-300
            @isset($icon) pl-10 pr-4 @else px-4 @endisset
            @error($name) border-danger-300 focus:border-danger-400 focus:ring-danger-300 @else border-secondary-300 focus:border-secondary-400 focus:ring-primary-400 @enderror">
        </div>
        @error($name)
            <label for={{ $id ?? $name }} class="text-sm text-danger-300">{{ $message }}</label>
        @enderror
    </div>
@endif
