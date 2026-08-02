<?php

namespace Paymenter\Extensions\Others\CyberpunkTheme\Livewire;

use App\Livewire\Component;
use Paymenter\Extensions\Others\CyberpunkTheme\Models\Post;
use Paymenter\Extensions\Others\CyberpunkTheme\Support\Config;

/**
 * Bloque del inicio con las últimas publicaciones de la comunidad.
 */
class CommunityPreview extends Component
{
    public function render()
    {
        $posts = collect();

        try {
            $posts = Post::with(['user', 'media'])
                ->where('approved', true)
                ->orderByDesc('pinned')
                ->orderByDesc('created_at')
                ->take(3)
                ->get();
        } catch (\Throwable $e) {
            // Tablas aún no migradas: no mostramos nada.
        }

        return view('cyberpunk::livewire.community-preview', [
            'posts' => $posts,
            'communityName' => Config::theme('community_name', 'Comunidad'),
            'communityUrl' => url('/' . Config::communitySlug()),
            'description' => Config::theme('community_description', 'Comparte tu experiencia con el hosting: fotos, vídeos y opiniones.'),
        ]);
    }
}
