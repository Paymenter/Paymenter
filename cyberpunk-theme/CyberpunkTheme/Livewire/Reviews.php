<?php

namespace Paymenter\Extensions\Others\CyberpunkTheme\Livewire;

use App\Livewire\Component;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;
use Paymenter\Extensions\Others\CyberpunkTheme\Models\Comment;
use Paymenter\Extensions\Others\CyberpunkTheme\Models\Like;
use Paymenter\Extensions\Others\CyberpunkTheme\Support\Config;
use Paymenter\Extensions\Others\CyberpunkTheme\Support\InteractsWithCommunity;
use Paymenter\Extensions\Others\CyberpunkTheme\Support\Reviews as ReviewsHelper;

/**
 * Apartado público de reseñas: todos los productos en un sitio, para que
 * cualquier usuario pueda dar like, opinar y responder a otras opiniones
 * sin tener que entrar producto por producto.
 */
class Reviews extends Component
{
    use InteractsWithCommunity;

    /** Producto abierto ahora mismo */
    #[Url(except: 0, as: 'producto')]
    public int $openProduct = 0;

    /** Filtro por categoría ('' = todas) */
    #[Url(except: '', as: 'cat')]
    public string $categoryFilter = '';

    #[Url(except: '', as: 'q')]
    public string $search = '';

    /** Orden del listado: valorados | comentados | nombre */
    #[Url(except: 'valorados', as: 'orden')]
    public string $sort = 'valorados';

    public string $body = '';

    public ?int $replyingTo = null;

    public array $replyBody = [];

    public function render()
    {
        $products = collect();
        $categories = collect();
        $stats = [];
        $liked = [];
        $comments = collect();
        $popular = [];

        try {
            $categories = Category::whereHas('products', fn ($q) => $q->where('hidden', false))
                ->orderBy('sort')
                ->get();

            $query = Product::with('category')
                ->where('hidden', false);

            if ($this->categoryFilter !== '') {
                $query->where('category_id', (int) $this->categoryFilter);
            }

            if (trim($this->search) !== '') {
                $query->where('name', 'like', '%' . trim($this->search) . '%');
            }

            $products = $query->orderBy('name')->get();

            $stats = ReviewsHelper::allStats();
            $popular = ReviewsHelper::popularProductIds();

            // Ordenamos en memoria: las estadísticas viven en las tablas de la
            // extensión y así no dependemos de un join contra el core.
            $products = match ($this->sort) {
                'comentados' => $products->sortByDesc(fn ($p) => $stats[$p->id]['comments'] ?? 0)->values(),
                'nombre' => $products,
                default => $products->sortByDesc(fn ($p) => $stats[$p->id]['likes'] ?? 0)->values(),
            };

            if (Auth::check()) {
                $liked = Like::where('user_id', Auth::id())
                    ->where('likeable_type', Product::class)
                    ->pluck('likeable_id')
                    ->map(fn ($id) => (int) $id)
                    ->all();
            }

            if ($this->openProduct > 0) {
                $comments = Comment::with(['user', 'replies.user'])
                    ->where('commentable_type', Product::class)
                    ->where('commentable_id', $this->openProduct)
                    ->whereNull('parent_id')
                    ->where('approved', true)
                    ->orderByDesc('created_at')
                    ->get();
            }
        } catch (\Throwable $e) {
            report($e);
        }

        return view('cyberpunk::livewire.reviews', [
            'products' => $products,
            'categories' => $categories,
            'stats' => $stats,
            'likedProducts' => $liked,
            'comments' => $comments,
            'popular' => $popular,
            'openedProduct' => $this->openProduct > 0 ? $products->firstWhere('id', $this->openProduct) : null,
        ])->layoutData([
            'title' => Config::theme('reviews_name', 'Reseñas'),
        ]);
    }

    // ------------------------------------------------------------------
    public function open(int $productId): void
    {
        $this->openProduct = $this->openProduct === $productId ? 0 : $productId;
        $this->body = '';
        $this->replyingTo = null;
    }

    public function toggleLike(int $productId): void
    {
        if (!$this->requireLogin('valorar un producto') || !$this->requireTables()) {
            return;
        }

        $this->runSafely(function () use ($productId) {
            $like = Like::where('user_id', Auth::id())
                ->where('likeable_type', Product::class)
                ->where('likeable_id', $productId)
                ->first();

            if ($like) {
                $like->delete();
            } else {
                Like::create([
                    'user_id' => Auth::id(),
                    'likeable_type' => Product::class,
                    'likeable_id' => $productId,
                ]);
            }

            ReviewsHelper::flush();
        }, 'No se pudo registrar tu "me gusta".');
    }

    public function comment(int $productId): void
    {
        if (!$this->requireLogin('dejar una reseña') || !$this->requireTables()) {
            return;
        }

        $body = trim($this->body);

        if (mb_strlen($body) < 3) {
            $this->notify(__('Escribe una reseña un poco más larga.'), 'error');

            return;
        }

        $this->runSafely(function () use ($productId, $body) {
            Comment::create([
                'user_id' => Auth::id(),
                'commentable_type' => Product::class,
                'commentable_id' => $productId,
                'content' => mb_substr($body, 0, 1500),
                'approved' => Config::bool('auto_moderate', true),
            ]);

            $this->body = '';
            ReviewsHelper::flush();

            $this->notify(__('¡Gracias por tu reseña!'));
        }, 'No se pudo publicar la reseña.');
    }

    public function reply(int $commentId): void
    {
        if (!$this->requireLogin('responder') || !$this->requireTables()) {
            return;
        }

        $body = trim($this->replyBody[$commentId] ?? '');

        if ($body === '') {
            return;
        }

        $this->runSafely(function () use ($commentId, $body) {
            $parent = Comment::find($commentId);

            if (!$parent) {
                return;
            }

            Comment::create([
                'user_id' => Auth::id(),
                'commentable_type' => Product::class,
                'commentable_id' => $parent->commentable_id,
                'parent_id' => $parent->id,
                'content' => mb_substr($body, 0, 1500),
                'approved' => Config::bool('auto_moderate', true),
            ]);

            $this->replyBody[$commentId] = '';
            $this->replyingTo = null;

            ReviewsHelper::flush();
        }, 'No se pudo publicar la respuesta.');
    }

    public function toggleCommentLike(int $commentId): void
    {
        if (!$this->requireLogin('valorar un comentario') || !$this->requireTables()) {
            return;
        }

        $this->runSafely(function () use ($commentId) {
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
        });
    }

    public function deleteComment(int $commentId): void
    {
        if (!Auth::check()) {
            return;
        }

        $this->runSafely(function () use ($commentId) {
            $comment = Comment::find($commentId);

            if (!$comment) {
                return;
            }

            /** @var \App\Models\User $user */
            $user = Auth::user();

            if ($user->id !== $comment->user_id && $user->role_id === null) {
                return;
            }

            $comment->delete();

            ReviewsHelper::flush();
        });
    }
}
