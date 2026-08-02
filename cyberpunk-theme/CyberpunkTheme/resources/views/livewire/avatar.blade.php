<div class="cyber-card cyber-clip p-6">
    <h2 class="text-lg font-bold mb-5 flex items-center gap-2">
        <x-ri-user-smile-fill class="size-5 text-primary" />
        Tu avatar
    </h2>

    <div class="flex flex-col sm:flex-row items-center gap-6">
        <img src="{{ $photo && method_exists($photo, 'temporaryUrl') ? $photo->temporaryUrl() : ($current ?? auth()->user()->avatar) }}"
            alt="avatar"
            class="size-24 rounded-2xl border-2 border-primary/50 object-cover cyber-neon">

        <div class="flex-grow w-full">
            <p class="text-sm text-base/60 mb-3">
                Sube tu propia foto o imagen. Se mostrará en la navegación, en los tickets y en la comunidad.
            </p>

            <label class="inline-flex items-center gap-2 text-sm font-semibold text-primary cursor-pointer hover:opacity-80">
                <x-ri-upload-cloud-2-fill class="size-5" />
                Elegir imagen
                <input type="file" wire:model="photo" accept="image/*" class="hidden">
            </label>
            <div wire:loading wire:target="photo" class="text-xs text-primary mt-2">Subiendo...</div>
            @error('photo') <p class="text-error text-xs mt-2">{{ $message }}</p> @enderror

            <div class="mt-4 flex flex-wrap gap-2">
                <x-button.primary wire:click="save" wire:loading.attr="disabled" class="!w-fit">
                    Guardar avatar
                </x-button.primary>
                @if($current)
                <x-button.secondary wire:click="remove" wire:confirm="¿Restablecer tu avatar por defecto?" class="!w-fit">
                    Quitar
                </x-button.secondary>
                @endif
            </div>
            <p class="text-xs text-base/40 mt-3">JPG, PNG, GIF o WEBP · máximo 4 MB</p>
        </div>
    </div>
</div>
