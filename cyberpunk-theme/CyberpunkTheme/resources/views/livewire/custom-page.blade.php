<div class="container mt-10 pb-16">
    <div class="cyber-card cyber-clip p-6 md:p-10">
        @if(($page['show_title'] ?? true))
        <div class="mb-8">
            <h1 class="text-3xl md:text-4xl font-black cyber-neon-text">
                <span class="cyber-glitch" data-text="{{ $page['title'] ?? '' }}">{{ $page['title'] ?? '' }}</span>
            </h1>
            @if(!empty($page['description']))
            <p class="mt-3 text-base/65 max-w-3xl">{{ $page['description'] }}</p>
            @endif
            <div class="cyber-divider mt-6"></div>
        </div>
        @endif

        <article class="prose dark:prose-invert max-w-none">
            {{-- El HTML lo escribe el administrador desde el panel --}}
            {!! $page['content'] ?? '' !!}
        </article>
    </div>
</div>
