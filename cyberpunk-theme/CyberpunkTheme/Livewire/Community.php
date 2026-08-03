<?php

namespace Paymenter\Extensions\Others\CyberpunkTheme\Livewire;

use App\Livewire\Component;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Paymenter\Extensions\Others\CyberpunkTheme\Support\InteractsWithCommunity;
use Livewire\Attributes\Url;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Paymenter\Extensions\Others\CyberpunkTheme\Models\Comment;
use Paymenter\Extensions\Others\CyberpunkTheme\Models\Like;
use Paymenter\Extensions\Others\CyberpunkTheme\Models\Post;
use Paymenter\Extensions\Others\CyberpunkTheme\Models\PostMedia;
use Paymenter\Extensions\Others\CyberpunkTheme\Support\Config;
use Paymenter\Extensions\Others\CyberpunkTheme\Support\PostCategories;

/**
 * Muro de la comunidad: publicaciones con imágenes y vídeos, likes,
 * comentarios y respuestas.
 */
class Community extends Component
{
    use InteractsWithCommunity, WithFileUploads, WithPagination;

    public string $title = '';

    public string $content = '';

    /** @var array<int, \Livewire\Features\SupportFileUploads\TemporaryUploadedFile> */
    public array $media = [];

    /** Categoría de la publicación nueva */
    public string $category = 'general';

    #[Url(except: 'recent', as: 'orden')]
    public string $sort = 'recent';

    /** Filtro por categoría del listado ('' = todas) */
    #[Url(except: '', as: 'cat')]
    public string $filter = '';

    /** Comentario nuevo por publicación: [postId => texto] */
    public array $commentBody = [];

    /** Respuesta nueva por comentario: [commentId => texto] */
    public array $replyBody = [];

    /** Comentario al que se está respondiendo */
    public ?int $replyingTo = null;

    /** Publicaciones con los comentarios abiertos */
    public array $openComments = [];

    public function rules(): array
    {
        return [
            'title' => 'nullable|string|max:120',
            'content' => 'required|string|min:3|max:2000',
            'category' => 'nullable|string|max:32',
            'media' => 'nullable|array|max:' . $this->mediaLimit(),
            'media.*' => 'file|mimes:jpg,jpeg,png,gif,webp,mp4,webm,mov,mkv|max:' . ($this->mediaMaxMb() * 1024),
        ];
    }

    public function mediaLimit(): int
    {
        return max(1, (int) Config::theme('community_media_limit', 4));
    }

    /**
     * Tamaño máximo real por archivo, en MB.
     *
     * No sirve de nada prometer 20 MB si el servidor sólo acepta 2: usamos
     * el menor entre lo que queremos y lo que PHP permite de verdad.
     */
    public function mediaMaxMb(): int
    {
        $limites = array_filter([
            self::iniMb('upload_max_filesize'),
            self::iniMb('post_max_size'),
        ]);

        $servidor = count($limites) > 0 ? min($limites) : 20;

        return max(1, min(20, $servidor));
    }

    private static function iniMb(string $key): ?int
    {
        $raw = trim((string) ini_get($key));

        if ($raw === '' || $raw === '-1' || $raw === '0') {
            return null;
        }

        $unidad = strtolower(substr($raw, -1));
        $valor = (float) $raw;

        $bytes = match ($unidad) {
            'g' => $valor * 1024 * 1024 * 1024,
            'm' => $valor * 1024 * 1024,
            'k' => $valor * 1024,
            default => $valor,
        };

        return (int) floor($bytes / 1024 / 1024);
    }

    /**
     * Se ejecuta cada vez que llega un archivo nuevo: valida sólo ése y
     * respeta el máximo, para que el usuario vea el fallo al momento.
     */
    public function updatedMedia(): void
    {
        $this->media = array_values(array_filter(
            $this->media,
            fn ($file) => $file instanceof \Illuminate\Http\UploadedFile
        ));

        // Nunca más archivos de los permitidos.
        if (count($this->media) > $this->mediaLimit()) {
            foreach (array_slice($this->media, $this->mediaLimit()) as $sobrante) {
                $this->deleteTemporary($sobrante);
            }

            $this->media = array_slice($this->media, 0, $this->mediaLimit());

            $this->notify(
                __('Sólo se pueden adjuntar :n archivos por publicación.', ['n' => $this->mediaLimit()]),
                'error'
            );
        }

        $this->announceMediaCount();

        $this->validateOnly('media.*');
    }

    /**
     * Le dice al formulario cuántos archivos hay ahora mismo, para que el
     * contador "x de 4" no se desincronice al quitar o publicar.
     */
    private function announceMediaCount(): void
    {
        $this->dispatch('cyber-media-count', total: count($this->media));
    }

    /**
     * Quitar un archivo de la lista antes de publicar.
     */
    public function removeMedia(string $filename): void
    {
        $this->media = array_values(array_filter($this->media, function ($file) use ($filename) {
            if (method_exists($file, 'getFilename') && $file->getFilename() === $filename) {
                $this->deleteTemporary($file);

                return false;
            }

            return true;
        }));

        $this->announceMediaCount();
    }

    private function deleteTemporary($file): void
    {
        try {
            if (method_exists($file, 'delete')) {
                $file->delete();
            }
        } catch (\Throwable $e) {
            // Da igual: el limpiador de Livewire lo borra pasado un día.
        }
    }

    public function render()
    {
        $posts = new LengthAwarePaginator([], 0, 10);
        $comments = [];
        $likedPosts = [];
        $likedComments = [];
        $error = null;

        try {
            $query = Post::with(['user', 'media'])
                ->where('approved', true);

            if ($this->filter !== '' && PostCategories::exists($this->filter)) {
                $query->where('category', $this->filter);
            }

            $query = match ($this->sort) {
                'liked' => $query->orderByDesc('likes_count')->orderByDesc('created_at'),
                'commented' => $query->orderByDesc('comments_count')->orderByDesc('created_at'),
                default => $query->orderByDesc('pinned')->orderByDesc('created_at'),
            };

            $posts = $query->paginate(10);

            foreach ($posts as $post) {
                if (in_array($post->id, $this->openComments, true)) {
                    $comments[$post->id] = Comment::with(['user', 'replies.user'])
                        ->where('commentable_type', Post::class)
                        ->where('commentable_id', $post->id)
                        ->whereNull('parent_id')
                        ->where('approved', true)
                        ->orderByDesc('created_at')
                        ->get();
                }
            }

            $likedPosts = $this->likedIds(Post::class);
            $likedComments = $this->likedIds(Comment::class);
        } catch (\Throwable $e) {
            // Normalmente: las migraciones de la extensión no se han ejecutado.
            report($e);
            $error = 'La comunidad todavía no está lista. Un administrador debe ejecutar las migraciones de la extensión (Admin → Extensions → Cyberpunk Theme → Reinstalar archivos).';
        }

        return view('cyberpunk::livewire.community', [
            'posts' => $posts,
            'commentsByPost' => $comments,
            'likedPosts' => $likedPosts,
            'likedComments' => $likedComments,
            'error' => $error,
            'categories' => PostCategories::all(),
            'counts' => $this->categoryCounts(),
            'mediaLimit' => $this->mediaLimit(),
            'mediaMaxMb' => $this->mediaMaxMb(),
        ])->layoutData([
            'title' => Config::theme('community_name', 'Comunidad'),
        ]);
    }

    /**
     * Nº de publicaciones por categoría (para los filtros).
     *
     * @return array<string, int>
     */
    private function categoryCounts(): array
    {
        try {
            return Post::where('approved', true)
                ->selectRaw('category, COUNT(*) as total')
                ->groupBy('category')
                ->pluck('total', 'category')
                ->map(fn ($n) => (int) $n)
                ->all();
        } catch (\Throwable $e) {
            return [];
        }
    }

    public function setFilter(string $category): void
    {
        $this->filter = ($this->filter === $category) ? '' : $category;
        $this->resetPage();
    }

    private function likedIds(string $type): array
    {
        if (!Auth::check()) {
            return [];
        }

        return Like::where('user_id', Auth::id())
            ->where('likeable_type', $type)
            ->pluck('likeable_id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    public function updatedSort(): void
    {
        $this->resetPage();
    }

    // ------------------------------------------------------------------
    public function publish(): void
    {
        if (!$this->requireLogin('participar') || !$this->requireTables()) {
            return;
        }

        $this->validate();

        $post = Post::create([
            'user_id' => Auth::id(),
            'title' => $this->title ?: null,
            'content' => $this->content,
            'category' => PostCategories::normalize($this->category),
            'approved' => Config::bool('auto_moderate', true),
        ]);

        $subidas = 0;
        $fallos = 0;

        foreach (array_slice($this->media, 0, $this->mediaLimit()) as $index => $file) {
            try {
                $extension = strtolower((string) $file->getClientOriginalExtension());
                $isVideo = in_array($extension, ['mp4', 'webm', 'mov'], true);

                $path = $file->store('cyberpunk/community', 'public');

                if (!$path) {
                    $fallos++;

                    continue;
                }

                PostMedia::create([
                    'post_id' => $post->id,
                    'type' => $isVideo ? 'video' : 'image',
                    'path' => $path,
                    'sort' => $index,
                ]);

                $subidas++;
            } catch (\Throwable $e) {
                report($e);
                $fallos++;
            }
        }

        $this->reset(['title', 'content', 'media', 'category']);
        $this->resetPage();

        // El contador de archivos del formulario vuelve a cero.
        $this->announceMediaCount();

        if ($fallos > 0) {
            $this->notify(
                __('La publicación se creó, pero :n archivo(s) no se pudieron guardar. Revisa los permisos de storage/app/public.', ['n' => $fallos]),
                'error'
            );

            return;
        }

        $this->notify($post->approved
            ? __('¡Publicado! Gracias por compartir.')
            : __('Tu publicación quedó pendiente de aprobación.'));
    }

    public function toggleLike(int $postId): void
    {
        if (!$this->requireLogin('participar') || !$this->requireTables()) {
            return;
        }

        $post = Post::find($postId);
        if (!$post) {
            return;
        }

        $like = Like::where('user_id', Auth::id())
            ->where('likeable_type', Post::class)
            ->where('likeable_id', $post->id)
            ->first();

        if ($like) {
            $like->delete();
            $post->decrement('likes_count');
        } else {
            Like::create([
                'user_id' => Auth::id(),
                'likeable_type' => Post::class,
                'likeable_id' => $post->id,
            ]);
            $post->increment('likes_count');
        }
    }

    public function toggleCommentLike(int $commentId): void
    {
        if (!$this->requireLogin('participar') || !$this->requireTables()) {
            return;
        }

        $comment = Comment::find($commentId);
        if (!$comment) {
            return;
        }

        $like = Like::where('user_id', Auth::id())
            ->where('likeable_type', Comment::class)
            ->where('likeable_id', $comment->id)
            ->first();

        if ($like) {
            $like->delete();
            $comment->decrement('likes_count');
        } else {
            Like::create([
                'user_id' => Auth::id(),
                'likeable_type' => Comment::class,
                'likeable_id' => $comment->id,
            ]);
            $comment->increment('likes_count');
        }
    }

    public function toggleComments(int $postId): void
    {
        if (in_array($postId, $this->openComments, true)) {
            $this->openComments = array_values(array_diff($this->openComments, [$postId]));
        } else {
            $this->openComments[] = $postId;
        }
    }

    public function comment(int $postId): void
    {
        if (!$this->requireLogin('participar') || !$this->requireTables()) {
            return;
        }

        $body = trim($this->commentBody[$postId] ?? '');

        if ($body === '') {
            return;
        }

        $post = Post::find($postId);
        if (!$post) {
            return;
        }

        Comment::create([
            'user_id' => Auth::id(),
            'commentable_type' => Post::class,
            'commentable_id' => $post->id,
            'content' => mb_substr($body, 0, 1500),
            'approved' => Config::bool('auto_moderate', true),
        ]);

        $post->increment('comments_count');

        $this->commentBody[$postId] = '';
        if (!in_array($postId, $this->openComments, true)) {
            $this->openComments[] = $postId;
        }
    }

    public function reply(int $commentId): void
    {
        if (!$this->requireLogin('participar') || !$this->requireTables()) {
            return;
        }

        $body = trim($this->replyBody[$commentId] ?? '');

        if ($body === '') {
            return;
        }

        $parent = Comment::find($commentId);
        if (!$parent) {
            return;
        }

        Comment::create([
            'user_id' => Auth::id(),
            'commentable_type' => $parent->commentable_type,
            'commentable_id' => $parent->commentable_id,
            'parent_id' => $parent->id,
            'content' => mb_substr($body, 0, 1500),
            'approved' => Config::bool('auto_moderate', true),
        ]);

        if ($parent->commentable_type === Post::class) {
            Post::where('id', $parent->commentable_id)->increment('comments_count');
        }

        $this->replyBody[$commentId] = '';
        $this->replyingTo = null;
    }

    public function deletePost(int $postId): void
    {
        $post = Post::find($postId);

        if (!$post || !$this->canManage($post->user_id)) {
            return;
        }

        // El modelo se encarga de borrar archivos, comentarios y likes.
        $post->delete();

        $this->notify(__('Publicación eliminada.'));
    }

    public function deleteComment(int $commentId): void
    {
        $comment = Comment::find($commentId);

        if (!$comment || !$this->canManage($comment->user_id)) {
            return;
        }

        $replies = $comment->replies()->count();

        if ($comment->commentable_type === Post::class) {
            Post::where('id', $comment->commentable_id)->decrement('comments_count', $replies + 1);
        }

        // El modelo borra las respuestas y los likes en cascada.
        $comment->delete();

        $this->notify(__('Comentario eliminado.'));
    }

    private function canManage(?int $ownerId): bool
    {
        if (!Auth::check()) {
            return false;
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        return $user->id === $ownerId || $user->role_id !== null;
    }
}
