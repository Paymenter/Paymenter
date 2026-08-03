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
 * Apartado público de reseñas con estrellas.
 *
 * Dos secciones:
 *  - "planes"  → cada plan con su nota media y sus reseñas
 *  - "general" → la opinión sobre el hosting en conjunto
 *
 * Para dejar una reseña hay que poner estrellas Y escribir: sin texto no se
 * guarda la puntuación, igual que en cualquier sitio de reseñas serio.
 */
class Reviews extends Component
{
    use InteractsWithCommunity;

    /** Apartado activo: planes | general */
    #[Url(except: 'planes', as: 'ver')]
    public string $section = 'planes';

    /** Plan abierto ahora mismo */
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

    /** Formulario de reseña */
    public int $rating = 0;

    public string $body = '';

    /** Formulario de la reseña general */
    public int $generalRating = 0;

    public string $generalBody = '';

    public ?int $replyingTo = null;

    public array $replyBody = [];

    public function mount(): void
    {
        $this->section = $this->section === 'general' ? 'general' : 'planes';

        $this->loadOwnReviews();
    }

    /**
     * Si el usuario ya dejó su reseña, el formulario aparece relleno para
     * que pueda cambiarla en vez de duplicarla.
     */
    private function loadOwnReviews(): void
    {
        if (!Auth::check()) {
            return;
        }

        $general = ReviewsHelper::userReview(Auth::id(), Comment::GENERAL, 0);

        if ($general) {
            $this->generalRating = (int) $general->rating;
            $this->generalBody = (string) $general->content;
        }

        if ($this->openProduct > 0) {
            $propia = ReviewsHelper::userReview(Auth::id(), Product::class, $this->openProduct);

            $this->rating = $propia ? (int) $propia->rating : 0;
            $this->body = $propia ? (string) $propia->content : '';
        }
    }

    public function render()
    {
        $products = collect();
        $categories = collect();
        $stats = [];
        $comments = collect();
        $generalReviews = collect();
        $popular = [];
        $likedComments = [];

        try {
            $categories = Category::whereHas('products', fn ($q) => $q->where('hidden', false))
                ->orderBy('sort')
                ->get();

            $query = Product::with('category')->where('hidden', false);

            if ($this->categoryFilter !== '') {
                $query->where('category_id', (int) $this->categoryFilter);
            }

            if (trim($this->search) !== '') {
                $query->where('name', 'like', '%' . trim($this->search) . '%');
            }

            $products = $query->orderBy('name')->get();

            $stats = ReviewsHelper::allStats();
            $popular = ReviewsHelper::popularProductIds();

            $products = match ($this->sort) {
                'comentados' => $products->sortByDesc(fn ($p) => $stats[$p->id]['count'] ?? 0)->values(),
                'nombre' => $products,
                default => $products
                    ->sortByDesc(fn ($p) => [$stats[$p->id]['average'] ?? 0, $stats[$p->id]['count'] ?? 0])
                    ->values(),
            };

            if ($this->openProduct > 0) {
                $comments = $this->reviewsFor(Product::class, $this->openProduct);
            }

            if ($this->section === 'general') {
                $generalReviews = $this->reviewsFor(Comment::GENERAL, 0);
            }

            $likedComments = $this->likedCommentIds();
        } catch (\Throwable $e) {
            report($e);
        }

        return view('cyberpunk::livewire.reviews', [
            'products' => $products,
            'categories' => $categories,
            'stats' => $stats,
            'comments' => $comments,
            'generalReviews' => $generalReviews,
            'generalStats' => ReviewsHelper::generalStats(),
            'popular' => $popular,
            'likedComments' => $likedComments,
            'openedProduct' => $this->openProduct > 0 ? $products->firstWhere('id', $this->openProduct) : null,
            'generalName' => Config::theme('general_reviews_name', 'El servicio en general'),
        ])->layoutData([
            'title' => Config::theme('reviews_name', 'Reseñas'),
        ]);
    }

    /**
     * Reseñas de un destino, las mejor valoradas y más recientes primero.
     */
    private function reviewsFor(string $type, int $id)
    {
        return Comment::with(['user', 'replies.user'])
            ->reviews()
            ->where('commentable_type', $type)
            ->where('commentable_id', $id)
            ->orderByDesc('created_at')
            ->get();
    }

    private function likedCommentIds(): array
    {
        if (!Auth::check()) {
            return [];
        }

        try {
            return Like::where('user_id', Auth::id())
                ->where('likeable_type', Comment::class)
                ->pluck('likeable_id')
                ->map(fn ($id) => (int) $id)
                ->all();
        } catch (\Throwable $e) {
            return [];
        }
    }

    // ------------------------------------------------------------------
    public function setSection(string $section): void
    {
        $this->section = $section === 'general' ? 'general' : 'planes';
        $this->replyingTo = null;
    }

    public function open(int $productId): void
    {
        $this->openProduct = $this->openProduct === $productId ? 0 : $productId;
        $this->rating = 0;
        $this->body = '';
        $this->replyingTo = null;

        $this->loadOwnReviews();
    }

    /**
     * Guarda (o actualiza) la reseña de un plan.
     */
    public function publishReview(int $productId): void
    {
        $this->saveReview(Product::class, $productId, $this->rating, $this->body);
    }

    /**
     * Guarda (o actualiza) la reseña sobre el servicio en general.
     */
    public function publishGeneralReview(): void
    {
        $this->saveReview(Comment::GENERAL, 0, $this->generalRating, $this->generalBody);
    }

    /**
     * Lógica común: sin estrellas o sin texto, no hay reseña.
     */
    private function saveReview(string $type, int $id, int $rating, string $body): void
    {
        if (!$this->requireLogin('dejar una reseña') || !$this->requireTables()) {
            return;
        }

        $body = trim($body);

        if ($rating < 1 || $rating > 5) {
            $this->notify(__('Elige cuántas estrellas le das.'), 'error');

            return;
        }

        if (mb_strlen($body) < 10) {
            $this->notify(__('Cuenta un poco tu experiencia (mínimo 10 caracteres) para poder guardar tu puntuación.'), 'error');

            return;
        }

        $this->runSafely(function () use ($type, $id, $rating, $body) {
            $existente = ReviewsHelper::userReview(Auth::id(), $type, $id);

            $datos = [
                'rating' => $rating,
                'content' => mb_substr($body, 0, 1500),
                'approved' => Config::bool('auto_moderate', true),
            ];

            if ($existente) {
                $existente->update($datos);
                $mensaje = __('Tu reseña se ha actualizado.');
            } else {
                Comment::create($datos + [
                    'user_id' => Auth::id(),
                    'commentable_type' => $type,
                    'commentable_id' => $id,
                ]);
                $mensaje = __('¡Gracias por tu reseña!');
            }

            ReviewsHelper::flush();

            $this->notify($mensaje);
        }, 'No se pudo guardar la reseña.');
    }

    /**
     * Respuesta a una reseña (sin estrellas).
     */
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
                'commentable_type' => $parent->commentable_type,
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

    /**
     * "Me ha resultado útil" sobre una reseña de otro usuario.
     */
    public function toggleCommentLike(int $commentId): void
    {
        if (!$this->requireLogin('marcar una reseña como útil') || !$this->requireTables()) {
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

            $propia = $comment->user_id === $user->id;
            $tipo = $comment->commentable_type;

            $comment->delete();

            ReviewsHelper::flush();

            // Si borró la suya, el formulario vuelve a quedar vacío.
            if ($propia) {
                if ($tipo === Comment::GENERAL) {
                    $this->generalRating = 0;
                    $this->generalBody = '';
                } else {
                    $this->rating = 0;
                    $this->body = '';
                }
            }
        });
    }
}
