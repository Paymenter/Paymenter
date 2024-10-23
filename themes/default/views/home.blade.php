<div>
    <div class="flex flex-col gap-6">
        <div class="mx-auto container bg-primary-800 p-4 rounded-md">
            <article class="prose prose-invert">
                {!! Str::markdown(theme('home_page_text', 'Welcome to Paymenter'), [
                'html_input' => 'strip',
                'allow_unsafe_links' => false,
                ]) !!}
            </article>
        </div>
        <div class="mx-auto container bg-primary-800 p-4 rounded-md">
            <h1 class="text-2xl font-semibold text-white">Products</h1>
            @foreach ($categories as $category)
                
                
                
            @endforeach
        </div>
    </div>
    {!! hook('pages.home') !!}
</div>