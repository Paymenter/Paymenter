<?php

namespace Paymenter\Extensions\Others\CyberpunkTheme\Livewire;

use App\Livewire\Component;
use Paymenter\Extensions\Others\CyberpunkTheme\Support\Config;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Renderiza una página personalizada creada desde el panel de administración.
 * El contenido HTML lo escribe el administrador, por eso se muestra tal cual.
 */
class CustomPage extends Component
{
    public string $slug = '';

    public array $page = [];

    public function mount(string $slug): void
    {
        $this->slug = $slug;

        $pages = Config::themeList('custom_pages');

        foreach ($pages as $page) {
            if (($page['slug'] ?? null) !== $slug) {
                continue;
            }

            if (isset($page['enabled']) && !filter_var($page['enabled'], FILTER_VALIDATE_BOOLEAN)) {
                continue;
            }

            $this->page = $page;
            break;
        }

        if (count($this->page) === 0) {
            throw new NotFoundHttpException;
        }
    }

    public function render()
    {
        return view('cyberpunk::livewire.custom-page')->layoutData([
            'title' => $this->page['title'] ?? '',
            'description' => $this->page['description'] ?? null,
        ]);
    }
}
