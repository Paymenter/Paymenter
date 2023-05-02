<nav class="bg-secondary-50 dark:bg-secondary-100 dark:border-0 border-b-2 border-secondary-200">
    <div class="max-w-[1650px] mx-auto block md:flex items-center gap-x-10 px-5">
        <a href="{{ route('index') }}" class="flex items-center text-secondary-900 font-semibold text-lg py-2 gap-x-2">
            <x-application-logo class="w-10" />
            {{ config('app.name', 'Paymenter') }}
        </a>
        <a href="{{ route('index') }}"
            class="md:px-2 py-3 flex items-center gap-x-1 hover:text-secondary-800 duration-300">
            {{ __('Home') }}
        </a>
        <button type="button" aria-expanded="true" data-dropdown-placement="bottom-start" aria-haspopup="true"
            data-dropdown-toggle="orders"
            class="relative md:px-2 py-3 flex items-center gap-x-1 hover:text-secondary-800 duration-300">
            {{ __('Shop') }} <i class="ri-arrow-down-s-line"></i>

            <div class="absolute left-0 hidden w-56 mt-2 origin-top-right bg-secondary-200 border border-secondary-300 rounded-md"
                role="menu" aria-orientation="vertical" aria-labelledby="product" tabindex="-1" id="orders">
                @foreach (App\Models\Category::all() as $category)
                    @if ($category->products->count() > 0)
                        <a href="{{ route('products', $category->slug) }}"
                            class="flex px-4 py-2 rounded text-secondary-700 hover:bg-secondary-100 hover:text-secondary-900"
                            role="menuitem" tabindex="-1" id="menu-item-0">{{ $category->name }}</a>
                    @endif
                @endforeach
            </div>

        </button>
        <a href="{{ route('announcements.index') }}"
            class="md:px-2 py-3 flex items-center gap-x-1 hover:text-secondary-800 duration-300">
            {{ __('Announcements') }}
        </a>
        <a href="{{ route('clients.tickets.index') }}"
            class="md:px-2 py-3 flex items-center gap-x-1 hover:text-secondary-800 duration-300">
            {{ __('Help Center') }}
        </a>
        <div class="ml-auto flex items-center gap-x-1 justify-center md:pb-0 pb-4">
            @if (count(session()->get('cart', [])) > 0)
                <a href="{{ route('checkout.index') }}" class="button button-secondary-outline !font-normal">
                    <i class="ri-shopping-bag-line"></i>
                    {{ count(session()->get('cart')) }}
                </a>
            @endif
            @auth
                <button type="button" aria-expanded="true" aria-haspopup="true" data-dropdown-placement="bottom-end"
                    data-dropdown-toggle="account" class="relative button button-primary md:flex-none flex-1">
                    {{ __('Account') }}
                    <div class="absolute left-0 hidden w-60 mt-2 origin-top-right bg-secondary-200 border border-secondary-300 rounded-md text-secondary-700 font-normal text-start"
                        role="menu" aria-orientation="vertical" aria-labelledby="product" tabindex="-1" id="account">
                        <a href="{{ route('clients.profile') }}"
                            class="flex items-center px-4 py-3 gap-x-4 overflow-hidden">
                            <img class="w-12 h-12 rounded-md"
                                src="https://www.gravatar.com/avatar/{{ md5(Auth::user()->email) }}?s=200&d=mp"
                                alt="{{ Auth::user()->name }}" />
                            <div>
                                <p class="leading-4">{{ Auth::user()->name }}</p>
                                <p class="text-sm">{{ Auth::user()->email }}</p>
                            </div>
                        </a>
                        <div class="px-2 pb-2">
                            <a href="/home" class="px-2 py-2 hover:bg-secondary-300 flex items-center gap-x-2 rounded"><i
                                    class="ri-layout-2-line"></i> {{ __('Client area') }}</a>
                            {{-- <a href="#" class="px-2 py-2 hover:bg-secondary-300 flex items-center gap-x-2 rounded"><i class="ri-instance-line"></i> {{ __('Services') }}</a> --}}
                            @if (Auth::user()->is_admin)
                                <a href="{{ route('admin.index') }}"
                                    class="px-2 py-2 hover:bg-secondary-300 flex items-center gap-x-2 rounded"><i
                                        class="ri-key-2-line"></i> {{ __('Admin area') }}</a>
                                <a href="{{ route('clients.api.index') }}"
                                    class="px-2 py-2 hover:bg-secondary-300 flex items-center gap-x-2 rounded"><i
                                        class="ri-code-s-slash-line"></i>
                                    {{ __('API') }}</a>
                            @endif
                            <hr class="mx-2 my-1 border-secondary-400" />
                            <a type="button" href="{{ route('logout') }}"
                                class="px-2 py-2 hover:bg-secondary-300 flex items-center gap-x-2 rounded"
                                onclick="event.preventDefault();document.getElementById('logout-form').submit();"><i
                                    class="ri-logout-box-line"></i> {{ __('Log Out') }}</a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                @csrf
                            </form>
                        </div>
                    </div>
                </button>
            @else
                <a href="{{ route('login') }}" class="button button-primary md:flex-none flex-1">
                    {{ __('Log In') }}
                </a>
            @endauth
            <button class="button button-secondary-outline !font-normal" id="theme-toggle">
                <i class="ri-sun-line hidden dark:block"></i>
                <i class="ri-moon-line dark:hidden"></i>
            </button>
            <script>
                // Change the icons inside the button based on previous settings
                if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia(
                        '(prefers-color-scheme: dark)').matches));

                var themeToggleBtn = document.getElementById('theme-toggle');
                themeToggleBtn.addEventListener('click', function() {
                    // if set via local storage previously
                    if (localStorage.getItem('theme')) {
                        if (localStorage.getItem('theme') === 'light') {
                            document.documentElement.classList.add('dark');
                            localStorage.setItem('theme', 'dark');
                        } else {
                            document.documentElement.classList.remove('dark');
                            localStorage.setItem('theme', 'light');
                        }
                        // if NOT set via local storage previously
                    } else {
                        if (document.documentElement.classList.contains('dark')) {
                            document.documentElement.classList.remove('dark');
                            localStorage.setItem('theme', 'light');
                        } else {
                            document.documentElement.classList.add('dark');
                            localStorage.setItem('theme', 'dark');
                        }
                    }
                });
            </script>
        </div>
    </div>
</nav>
