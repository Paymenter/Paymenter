@props([
    'model' => 'rating',        // propiedad Livewire con las estrellas
    'body' => 'body',           // propiedad Livewire con el texto
    'rating' => 0,
    'value' => '',
    'action' => 'publishReview',
    'own' => null,              // reseña que el usuario ya dejó, si la hay
    'placeholder' => 'Cuenta tu experiencia...',
    'min' => 10,
])

@auth
<div class="rounded-xl border border-neutral bg-background/40 p-4 sm:p-5">
    <div class="flex items-center justify-between gap-3 flex-wrap">
        <p class="font-bold">
            {{ $own ? 'Tu reseña' : 'Deja tu reseña' }}
        </p>
        @if($own)
        <span class="inline-flex items-center gap-1.5 text-xs text-base/50">
            <x-ri-time-line class="size-3.5" />
            Publicada {{ $own->created_at->diffForHumans() }}
        </span>
        @endif
    </div>

    <p class="mt-1 text-xs text-base/50">
        Pon tus estrellas <strong>y</strong> escribe por qué: sin reseña no se guarda la puntuación.
    </p>

    <div class="mt-4">
        <x-cyber.stars-input :model="$model" :value="$rating" />
    </div>

    {{-- El botón sólo se activa con estrellas Y texto suficiente. --}}
    <div class="mt-4"
        x-data="{
            nota: @entangle($model),
            texto: @entangle($body),
            min: {{ (int) $min }},
            get largo() { return (this.texto || '').trim().length },
            get listo() { return this.nota > 0 && this.largo >= this.min },
        }">

        <textarea wire:model.live.debounce.300ms="{{ $body }}" rows="3" maxlength="1500"
            placeholder="{{ $placeholder }}"
            class="w-full rounded-lg border border-neutral bg-background/60 text-base px-4 py-3 text-sm focus:border-primary focus:ring-0"></textarea>

        <div class="mt-2 flex flex-wrap items-center justify-between gap-3">
            <p class="text-xs" :class="listo ? 'text-success' : 'text-base/45'">
                <span x-show="nota === 0" x-cloak>Falta elegir las estrellas.</span>
                <span x-show="nota > 0 && largo < min" x-cloak>
                    Escribe al menos <span x-text="min - largo"></span> caracteres más.
                </span>
                <span x-show="listo" x-cloak>Todo listo para publicar.</span>
            </p>

            <button type="button" wire:click="{{ $action }}"
                wire:loading.attr="disabled"
                :disabled="!listo"
                class="inline-flex items-center gap-2 rounded-lg px-5 py-2.5 text-sm font-bold transition"
                :class="listo
                    ? 'bg-primary text-inverted hover:opacity-90 cursor-pointer'
                    : 'bg-neutral/50 text-base/40 cursor-not-allowed'">
                <x-ri-star-smile-fill class="size-4" />
                {{ $own ? 'Actualizar mi reseña' : 'Publicar reseña' }}
            </button>
        </div>
    </div>
</div>
@else
<div class="rounded-xl border border-neutral bg-background/40 p-6 text-center">
    <x-ri-star-smile-line class="size-9 mx-auto text-base/25" />
    <p class="mt-3 text-sm text-base/65">
        <a href="{{ route('login') }}" wire:navigate class="text-primary font-semibold hover:underline">Inicia sesión</a>
        para puntuar con estrellas y dejar tu reseña.
    </p>
</div>
@endauth
