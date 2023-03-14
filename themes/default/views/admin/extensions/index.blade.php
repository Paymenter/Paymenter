<x-admin-layout>
    <x-slot name="title">
        {{ __('Extensions') }}
    </x-slot>

    <div class="m-10 flex items-baseline relative">
        <h2 class="text-2xl font-semibold leading-tight text-gray-800 dark:text-darkmodetext">
            {{ __('Extensions and Gateways') }}
        </h2>
        <!-- Download Extension -->
        <div class="ml-4 absolute right-0">
            <button type="button" data-modal-target="defaultModal" onclick="getExtensions()"
                data-modal-toggle="defaultModal" class="form-submit float-right">
                {{ __('Download Extension') }}
            </button>
        </div>
        @if (session('verify'))
            <script>
                document.addEventListener("DOMContentLoaded", function(event) {
                    const options = {};
                    const $targetEl = document.querySelector('#popup-modal');
                    const modal = new Flowbite.default.Modal($targetEl, options);

                    modal.show();
                });
            </script>
            <button type="button" data-modal-target="popup-modal" data-modal-toggle="popup-modal"
                class="hidden"></button>
            <div id="popup-modal" tabindex="-1"
                class="fixed top-0 left-0 right-0 z-50 hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-modal md:h-full">
                <div class="relative w-full h-full max-w-md md:h-auto">
                    <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                        <button type="button"
                            class="absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-800 dark:hover:text-white"
                            data-modal-hide="popup-modal">
                            <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                        <form action="{{ route('admin.extensions.download') }}" method="POST">
                            @csrf
                            <input type="hidden" name="name" value="{{ old('name') }}">
                            <input type="hidden" name="verify" value="1">
                            <div class="p-6 text-center">
                                <svg aria-hidden="true" class="mx-auto mb-4 text-gray-400 w-14 h-14 dark:text-gray-200"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                    </path>
                                </svg>
                                <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">
                                    The extension is already found!<br> Do you want to override
                                    it?</h3>
                                <button type="submit"
                                    class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center mr-2">
                                    Yes, do it!
                                </button>
                                <button data-modal-hide="popup-modal" type="button"
                                    class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">No,
                                    cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        <div id="defaultModal" tabindex="-1" aria-hidden="true"
            class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-modal md:h-full">
            <div class="relative w-full h-full max-w-2xl md:h-auto">
                <form action="{{ route('admin.extensions.download') }}" method="POST">
                    @csrf
                    <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                        <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                {{ __('Download Extension') }}
                            </h3>
                            <button type="button"
                                class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white"
                                data-modal-hide="defaultModal">
                                <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <span class="sr-only">Close modal</span>
                            </button>
                        </div>
                        <div class="p-6 space-y-6">
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                                    {{ __('Extension Name') }}
                                </label>
                                <select name="name"
                                    class="dark:bg-darkmode2 dark:text-darkmodetext shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    id="name">
                                    <option value="" disabled selected>
                                        {{ __('Select Extension') }}</option>
                                </select>
                                <script>
                                    function getExtensions() {
                                        if (document.getElementById("name").options.length == 1) {
                                            loadExtensions();
                                        }
                                    }

                                    function loadExtensions() {
                                        fetch('https://api.github.com/repos/paymenter/extensions/contents/Gateways')
                                            .then(response => response.json())
                                            .then(data => {
                                                data.forEach(extension => {
                                                    var option = document.createElement("option");
                                                    option.text = extension.name + " (Gateway)";
                                                    option.value = extension.name + "-Gateways";
                                                    document.getElementById("name").appendChild(option);
                                                });
                                            });

                                        fetch('https://api.github.com/repos/paymenter/extensions/contents/Servers')
                                            .then(response => response.json())
                                            .then(data => {
                                                data.forEach(extension => {
                                                    var option = document.createElement("option");
                                                    option.text = extension.name + " (Server)";
                                                    option.value = extension.name + "-Servers";
                                                    document.getElementById("name").appendChild(option);
                                                });
                                            });
                                    }
                                </script>
                            </div>
                        </div>
                        <div
                            class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
                            <button type="submit" class="form-submit">Download</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @if (!$servers)
        <p class="dark:bg-darkmode2 dark:text-darkmodetext text-gray-600 px-3 rounded-md text-xl m-4">
            {{ __('No extensions found') }}
        </p>
    @else
        <table class="min-w-full divide-y divide-gray-200 w-full" id="table">
            <thead class="dark:bg-darkmode bg-gray-50">
                <tr>
                    <th scope="col"
                        class="dark:text-darkmodetext px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ __('Name') }}</th>
                    <th scope="col"
                        class="dark:text-darkmodetext px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ __('Enabled?') }}</th>
                    <th scope="col"
                        class="dark:text-darkmodetext px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ __('Edit') }}</th>
                </tr>
            </thead>
            <tbody class="dark:bg-darkmode bg-white divide-y divide-gray-200">
                <tr class="dark:bg-darkmode">
                    <td colspan="3"
                        class="dark:text-darkmodetext px-6 py-4 whitespace-nowrap text-gray-500 font-bold text-lg text-center">
                        Servers</td>
                </tr>
                @foreach ($servers as $extensio)
                    @if ($extensio == '.' || $extensio == '..')
                        @continue
                    @endif
                    <tr class="dark:bg-darkmode">
                        <td class="dark:text-darkmodetext px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $extensio }}</td>
                        <td class="dark:text-darkmodetext px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if (App\Models\Extension::where('name', $extensio)->get()->first()->enabled)
                                {{ __('Yes') }}
                            @else
                                {{ __('No') }}
                            @endif
                        </td>
                        <td class="dark:text-darkmodetext px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <a href="{{ route('admin.extensions.edit', ['server', $extensio]) }}"
                                class="text-indigo-600 hover:text-indigo-900 hover:bg-button p-2 rounded-lg">{{ __('Edit') }}</a>
                        </td>
                    </tr>
                @endforeach
                <tr class="dark:bg-darkmode">
                    <td colspan="3"
                        class="dark:text-darkmodetext px-6 py-4 whitespace-nowrap text-gray-500 font-bold text-lg text-center">
                        Gateways</td>
                </tr>
                @foreach ($gateways as $gateway)
                    @if ($gateway == '.' || $gateway == '..')
                        @continue
                    @endif
                    <tr>
                        <td class="dark:text-darkmodetext px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $gateway }}</td>
                        <td class="dark:text-darkmodetext px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if (App\Models\Extension::where('name', $gateway)->get()->first()->enabled)
                                {{ __('Yes') }}
                            @else
                                {{ __('No') }}
                            @endif
                        </td>
                        <td class="dark:text-darkmodetext px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <a href="{{ route('admin.extensions.edit', ['gateway', $gateway]) }}"
                                class="dark:bg-darkmodebutton text-indigo-600 hover:text-indigo-900 hover:bg-button p-2 rounded-lg">{{ __('Edit') }}</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    @endif
</x-admin-layout>
