<x-admin-layout title="Editing {{ $configurableOptionGroup->name }}">
    <h1 class="text-2xl font-bold dark:text-darkmodetext">{{ __('Editing') }} {{ $configurableOptionGroup->name }}</h1>
    <div class="p-6">
        <!-- create a new configurable option group -->
        <form method="POST" action="{{ route('admin.configurable-options.update', $configurableOptionGroup->id) }}">
            @csrf
            <x-input id="name" class="block mt-1 w-full" type="text" name="name" label="Name" placeholder="Name"
                required autofocus value="{{ $configurableOptionGroup->name }}" />
            <x-input id="description" class="block mt-1 w-full" type="text" name="description" label="Description"
                placeholder="Description" required autofocus value="{{ $configurableOptionGroup->description }}" />
            <!-- MultiSelect -->
            <x-input id="products" class="block mt-1 w-full" type="select" name="products[]"
                label="Products" placeholder="Products" required multiple>
                @foreach ($products as $product)
                    <option value="{{ $product->id }}" @if (in_array($product->id, $configurableOptionGroup->products)) selected @endif>
                        {{ $product->name }}</option>
                @endforeach
            </x-input>
            <button type="submit" class="button button-success mt-4 w-full">
                {{ __('Update') }}
            </button>
        </form>
    </div>
    <h1 class="text-2xl font-bold dark:text-darkmodetext">{{ __('Configurable Options') }}</h1>
    <x-modal title="Create new Configurable option" id="defaultModal" class="modal">
        <form method="POST"
            action="{{ route('admin.configurable-options.options.create', $configurableOptionGroup->id) }}">
            @csrf
            <x-input id="name" class="block mt-1 w-full" type="text" name="name" label="Name"
                placeholder="Name" required autofocus />
            <button type="submit" class="button button-success mt-4 w-full">
                {{ __('Create') }}
            </button>
        </form>
    </x-modal>
    <!-- Display the configurable options -->
    <div class="p-6">
        <button type="button" class="button button-primary mt-4" data-modal-target="defaultModal"
            data-modal-toggle="defaultModal">
            {{ __('Create new Option') }}
        </button>
        <table id="clientdatatable" class="table-auto w-full">
            <thead>
                <tr>
                    <th>
                        {{ __('ID') }}
                    </th>
                    <th>
                        {{ __('Name') }}
                    </th>
                    <th>
                        {{ __('Created At') }}
                    </th>
                    <th>
                        {{ __('Edit') }}
                    </th>
                    <th>
                        {{ __('Delete') }}
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($configurableOptions as $configurableOption)
                    <tr>
                        <td>
                            {{ $configurableOption->id }}
                        </td>
                        <td>
                            {{ $configurableOption->name }}
                        </td>
                        <td>
                            {{ $configurableOption->created_at }}
                        </td>
                        <td>
                            <x-modal id="editModal{{ $configurableOption->id }}"
                                title="Edit {{ $configurableOption->name }}" fullWidth>
                                <form method="POST"
                                    action="{{ route('admin.configurable-options.options.update', ['configurableOptionGroup' => $configurableOptionGroup->id, 'configurableOption' => $configurableOption->id]) }}">
                                    @csrf
                                    <div class="flex flex-row gap-4">
                                        <x-input id="name" class="block mt-1 w-full" type="text" name="name"
                                            label="Name" placeholder="Name" required
                                            value="{{ $configurableOption->name }}" />
                                        <x-input id="type" class="block mt-1 w-full" type="select" name="type"
                                            label="Type" placeholder="Type" required>
                                            <option value="select" @if ($configurableOption->type == 'select') selected @endif>
                                                Select</option>
                                            <option value="radio" @if ($configurableOption->type == 'radio') selected @endif>
                                                Radio</option>
                                            <option value="checkbox" @if ($configurableOption->type == 'checkbox') selected @endif>
                                                Checkbox</option>
                                            <option value="quantity" @if ($configurableOption->type == 'quantity') selected @endif>
                                                Quantity</option>
                                            <option value="slider" @if ($configurableOption->type == 'slider') selected @endif>
                                                Slider</option>
                                            <option value="text" @if ($configurableOption->type == 'text') selected @endif>
                                                Text</option>
                                        </x-input>
                                    </div>
                                    <div class="flex flex-row gap-4">
                                        <x-input id="order" class="block mt-1 w-full" type="text" name="order"
                                            label="Order" placeholder="Order" required
                                            value="{{ $configurableOption->order }}" />
                                        <div class="items-center flex" class="block mt-1 w-full">
                                            <x-input type="hidden" value="0" name="hidden" />
                                            <x-input id="hidden" class="block mt-1 w-full" type="checkbox"
                                                :checked="$configurableOption->hidden == 1 ? true : false" name="hidden"
                                                label="Hidden" placeholder="Hidden" value="1" />
                                        </div>

                                    </div>
                                    @foreach ($configurableOption->configurableOptionInputs as $key => $option)
                                        @php $pricing = $option->configurableOptionInputPrice; @endphp
                                        <div class="content-box mt-2 @if(in_array($configurableOption->type, ['text', 'checkbox', 'quantity']) && $key > 0) hidden @endif">
                                            <div class="flex flex-row text-sm gap-4 mt-1">
                                                <x-input label="Name" type="text" class="block mt-1 w-full"
                                                    name="option[{{ $option->id }}][name]" placeholder="Name"
                                                    value="{{ $option->name }}" />
                                                <div class="flex items-end">
                                                    <x-input label="Order" type="text" class="block mt-1 w-full"
                                                        name="option[{{ $option->id }}][order]" placeholder="Order"
                                                        value="{{ $option->order }}" />
                                                    <button type="button" class="button button-danger h-min ml-1"
                                                        onclick="event.preventDefault(); document.getElementById('deleteOption{{ $option->id }}').submit();">
                                                        {{ __('Delete') }}
                                                    </button>
                                                </div>
                                                <div class="items-center flex" class="block mt-1 w-full">
                                                    <x-input label="Hidden" type="checkbox"
                                                        name="option[{{ $option->id }}][hidden]"
                                                        :checked="$option->hidden == 1 ? true : false"
                                                        value="1" />
                                                </div>
                                            </div>
                                            <div class="flex flex-row text-sm gap-4 mt-2">
                                                <x-input label="Monthly/One-Time" type="text"
                                                    name="option[{{ $option->id }}][pricing][monthly]"
                                                    placeholder="Monthly" value="{{ $pricing['monthly'] ?? '' }}" />
                                                <x-input label="Quarterly" type="text"
                                                    name="option[{{ $option->id }}][pricing][quarterly]"
                                                    placeholder="Quarterly"
                                                    value="{{ $pricing['quarterly'] ?? '' }}" />
                                                <x-input label="Semi-Annually" type="text"
                                                    name="option[{{ $option->id }}][pricing][semi_annually]"
                                                    placeholder="Semi-Annually"
                                                    value="{{ $pricing['semi_annually'] ?? '' }}" />
                                                <x-input label="Annually" type="text"
                                                    name="option[{{ $option->id }}][pricing][annually]"
                                                    placeholder="Annually"
                                                    value="{{ $pricing['annually'] ?? '' }}" />
                                                <x-input label="Biennially" type="text"
                                                    name="option[{{ $option->id }}][pricing][biennially]"
                                                    placeholder="Biennially"
                                                    value="{{ $pricing['biennially'] ?? '' }}" />
                                                <x-input label="Triennially" type="text"
                                                    name="option[{{ $option->id }}][pricing][triennially]"
                                                    placeholder="Triennially"
                                                    value="{{ $pricing['triennially'] ?? '' }}" />
                                            </div>
                                            <div class="flex flex-row text-sm gap-4 mt-2">
                                                <x-input label="Monthly/One-time Setup Fee" type="text"
                                                    name="option[{{ $option->id }}][pricing][monthly_setup]"
                                                    placeholder="Monthly/One-time Setup Fee"
                                                    value="{{ $pricing['monthly_setup'] ?? '' }}" />
                                                <x-input label="Quarterly Setup Fee" type="text"
                                                    name="option[{{ $option->id }}][pricing][quarterly_setup]"
                                                    placeholder="Quarterly Setup Fee"
                                                    value="{{ $pricing['quarterly_setup'] ?? '' }}" />
                                                <x-input label="Semi-Annually Setup Fee" type="text"
                                                    name="option[{{ $option->id }}][pricing][semi_annually_setup]"
                                                    placeholder="Semi-Annually Setup Fee"
                                                    value="{{ $pricing['semi_annually_setup'] ?? '' }}" />
                                                <x-input label="Annually Setup Fee" type="text"
                                                    name="option[{{ $option->id }}][pricing][annually_setup]"
                                                    placeholder="Annually Setup Fee"
                                                    value="{{ $pricing['annually_setup'] ?? '' }}" />
                                                <x-input label="Biennially Setup Fee" type="text"
                                                    name="option[{{ $option->id }}][pricing][biennially_setup]"
                                                    placeholder="Biennially Setup Fee"
                                                    value="{{ $pricing['biennially_setup'] ?? '' }}" />
                                                <x-input label="Triennially Setup Fee" type="text"
                                                    name="option[{{ $option->id }}][pricing][triennially_setup]"
                                                    placeholder="Triennially Setup Fee"
                                                    value="{{ $pricing['triennially_setup'] ?? '' }}" />
                                            </div>
                                        </div>
                                    @endforeach
                                    <!-- Add new input -->
                                    <button type="submit" class="button button-success mt-4 w-full">
                                        {{ __('Update') }}
                                    </button>
                                </form>
                                <form method="POST"
                                    action="{{ route('admin.configurable-options.options.inputs.create', ['configurableOptionGroup' => $configurableOptionGroup->id, 'configurableOption' => $configurableOption->id]) }}">
                                    @csrf

                                    <button type="submit" class="button button-primary mt-4"
                                        id="addInput{{ $configurableOption->id }}">
                                        {{ __('Add new input') }}
                                    </button>
                                </form>
                                @foreach ($configurableOption->configurableOptionInputs as $option)
                                    <form method="POST" class="hidden"
                                        action="{{ route('admin.configurable-options.options.inputs.destroy', ['configurableOptionGroup' => $configurableOptionGroup->id, 'configurableOption' => $configurableOption->id, 'configurableOptionInput' => $option->id]) }}"
                                        id="deleteOption{{ $option->id }}">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                @endforeach
                            </x-modal>
                            <button type="button" class="button button-primary"
                                id="editModalButton{{ $configurableOption->id }}"
                                data-modal-target="editModal{{ $configurableOption->id }}"
                                data-modal-toggle="editModal{{ $configurableOption->id }}">
                                {{ __('Edit') }}
                            </button>
                        </td>
                        <td>
                            <button type="button" class="button button-danger"
                                onclick="event.preventDefault(); document.getElementById('deleteForm{{ $configurableOption->id }}').submit();">
                                {{ __('Delete') }}
                            </button>
                            <form id="deleteForm{{ $configurableOption->id }}"
                                action="{{ route('admin.configurable-options.options.destroy', ['configurableOptionGroup' => $configurableOptionGroup->id, 'configurableOption' => $configurableOption->id]) }}"
                                method="POST" class="hidden">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <script>
        var open = '{{ session()->get('open') }}';
        if (open) {
            document.addEventListener("DOMContentLoaded", function(event) {
                setTimeout(() => {
                    document.getElementById('editModalButton' + open).click();
                }, 500);
            });
        }
    </script>
</x-admin-layout>
