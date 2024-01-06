<x-admin-layout title="{{ __('Browse extensions') }}">
    <div class="flex flex-col md:flex-row justify-center md:justify-between">
        <h1 class="text-2xl font-bold">{{ __('Browse extensions') }}</h1>
        <input type="text" class="form-input md:w-1/5 h-2/3" placeholder="{{ __('Search') }}" id="extensionSearch">
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-3">
        @foreach ($extensions as $extension)
            @php $extension = (object) $extension; @endphp
            <div class="max-h-md extension" data-title="{{ $extension->name }}" data-slogan="{{ $extension->slogan }}">
                <div class="!bg-secondary-200 content-box w-full flex flex-row gap-x-3 h-full justify-center">
                    <div class="flex flex-row gap-x-3 h-full w-full">

                        <div class="w-fit h-full flex py-2 items-center">
                            <img src="{{ config('app.marketplace') . '../../storage/' . $extension->icon }}" alt="{{ $extension->name }}" class="w-[128px] h-auto rounded-md drop-shadow-lg">
                        </div>

                        <div class="flex flex-col h-full w-full">
                            <h1 class="text-xl font-bold">{{ $extension->name }}</h1>
                            <span>{{ $extension->slogan }}</span>
                            <div class="relative mt-12 h-full w-full">
                                <div class="absolute bottom-0 w-full">
                                    <div class="flex flex-row justify-between items-center relative w-full">
                                        <div>
                                            <div>
                                                <span class="font-semibold">{{ __('Price') }}: </span>
                                                {{ $extension->price == 0?"Free":$extension->price . "$" }}
                                            </div>
                                            <div>
                                                <span class="font-semibold">{{ __('Type') }}: </span>
                                                {{ ucFirst($extension->type) }}
                                            </div>
                                        </div>
                                        @if ($extension->price == 0)
                                            <div class="flex h-full">
                                                <form action="{{ route('admin.extensions.install', $extension->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="button button-primary">
                                                        <i class="ri-download-2-line text-xs"></i>
                                                        <span class="hidden 2xl:inline-flex">{{ __('Download') }}</span>
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <div class="flex h-full">
                                                <a href="{{'https://market.paymenter.org/extensions/' . $extension->id  . '/' . $extension->name }}" class="button button-primary">
                                                    <i class="ri-wallet-3-fill text-xs"></i>
                                                    <span class="hidden 2xl:inline-flex">{{ __('Buy') }}</span>
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <script>
        const searchInput = document.getElementById('extensionSearch');
        const extensionTitles = document.querySelectorAll('.extension');
        const extensionSlogans = document.querySelectorAll('[data-slogan]');

        searchInput.addEventListener('input', function () {
            const searchTerm = searchInput.value.toLowerCase();

            extensionTitles.forEach(title => {
                const titleText = title.getAttribute('data-title').toLowerCase();
                const sloganText = title.getAttribute('data-slogan').toLowerCase();

                if (titleText.includes(searchTerm) || sloganText.includes(searchTerm)) {
                    title.closest('.max-h-md').style.display = 'block';
                } else {
                    title.closest('.max-h-md').style.display = 'none';
                }
            });
        });
    </script>


</x-admin-layout>
