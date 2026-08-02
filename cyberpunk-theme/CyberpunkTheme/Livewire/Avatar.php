<?php

namespace Paymenter\Extensions\Others\CyberpunkTheme\Livewire;

use App\Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\WithFileUploads;
use Paymenter\Extensions\Others\CyberpunkTheme\Support\Avatars;

/**
 * Subida de avatar personalizado del usuario.
 */
class Avatar extends Component
{
    use WithFileUploads;

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
        if (!Auth::check()) {
            return;
        }

        $this->validate();

        if (!$this->photo) {
            $this->notify(__('Selecciona una imagen primero.'), 'error');

            return;
        }

        $path = $this->photo->store('cyberpunk/avatars', 'public');

        Avatars::store(Auth::user(), $path);

        $this->reset('photo');

        $this->notify(__('Avatar actualizado.'));
    }

    public function remove(): void
    {
        if (!Auth::check()) {
            return;
        }

        Avatars::remove(Auth::user());

        $this->notify(__('Avatar restablecido.'));
    }
}
