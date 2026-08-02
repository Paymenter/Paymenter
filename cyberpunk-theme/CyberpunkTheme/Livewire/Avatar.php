<?php

namespace Paymenter\Extensions\Others\CyberpunkTheme\Livewire;

use App\Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\WithFileUploads;
use Paymenter\Extensions\Others\CyberpunkTheme\Support\InteractsWithCommunity;
use Paymenter\Extensions\Others\CyberpunkTheme\Support\Avatars;

/**
 * Subida de avatar personalizado del usuario.
 */
class Avatar extends Component
{
    use InteractsWithCommunity, WithFileUploads;

    #[Validate('nullable|image|mimes:jpg,jpeg,png,gif,webp|max:4096')]
    public $photo;

    public function render()
    {
        return view('cyberpunk::livewire.avatar', [
            'current' => Auth::check() ? Avatars::url(Auth::user()) : null,
        ]);
    }

    public function save(): void
    {
        if (!$this->requireLogin('cambiar tu avatar') || !$this->requireTables()) {
            return;
        }

        $this->validate();

        if (!$this->photo) {
            $this->notify(__('Selecciona una imagen primero.'), 'error');

            return;
        }

        $this->runSafely(function () {
            $path = $this->photo->store('cyberpunk/avatars', 'public');

            if (!$path) {
                $this->notify(__('No se pudo guardar la imagen. Revisa los permisos de storage/app/public.'), 'error');

                return;
            }

            Avatars::store(Auth::user(), $path);

            $this->reset('photo');

            $this->notify(__('Avatar actualizado.'));
        }, 'No se pudo actualizar el avatar.');
    }

    public function remove(): void
    {
        if (!$this->requireLogin('cambiar tu avatar') || !$this->requireTables()) {
            return;
        }

        $this->runSafely(function () {
            Avatars::remove(Auth::user());

            $this->notify(__('Avatar restablecido.'));
        }, 'No se pudo restablecer el avatar.');
    }
}
