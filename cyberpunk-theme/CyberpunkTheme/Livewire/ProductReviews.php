<?php

namespace Paymenter\Extensions\Others\CyberpunkTheme\Livewire;

use App\Livewire\Component;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Paymenter\Extensions\Others\CyberpunkTheme\Models\Comment;
use Paymenter\Extensions\Others\CyberpunkTheme\Models\Like;
use Paymenter\Extensions\Others\CyberpunkTheme\Support\Config;
use Paymenter\Extensions\Others\CyberpunkTheme\Support\InteractsWithCommunity;
use Paymenter\Extensions\Others\CyberpunkTheme\Support\Reviews;

/**
 * Reseñas con estrellas en la página de un plan.
 *
 * Para puntuar hay que escribir: las estrellas solas no se guardan.
 */
class ProductReviews extends Component
{
    use InteractsWithCommunity;

    public int $productId;

    public int $rating = 0;

    public string $body = '';

    public ?int $replyingTo = null;

    public array $replyBody = [];

    public bool $showAll = false;

    public function mount(int $productId): void
    {
        $this->productId = $productId;

        $propia = Auth::check()
            ? Reviews::userReview(Auth::id(), Product::class, $productId)
            : null;

        if ($propia) {
            $this->rating = (int) $propia->rating;
            $this->body = (string) $propia->content;
        }
    }

    public function render()
    {
        $comments = collect();
        $stats = Reviews::EMPTY;
        $likedComments = [];

        try {
            $query = Comment::with(['user', 'replies.user'])
                ->reviews()
                ->where('commentable_type', Product::class)
                ->where('commentable_id', $this->productId)
                ->orderByDesc('created_at');

            $comments = $this->showAll ? $query->get() : $query->take(5)->get();

            $stats = Reviews::stats($this->productId);

            if (Auth::check()) {
                $likedComments = Like::where('user_id', Auth::id())
                    ->where('likeable_type', Comment::class)
                    ->pluck('likeable_id')
                    ->map(fn ($id) => (int) $id)
                    ->all();
            }
        } catch (\Throwable $e) {
            // Tablas aún no migradas.
        }

        return view('cyberpunk::livewire.product-reviews', [
            'comments' => $comments,
            'stats' => $stats,
            'likedComments' => $likedComments,
            'ownReview' => Auth::check()
                ? Reviews::userReview(Auth::id(), Product::class, $this->productId)
                : null,
        ]);
    }

    /**
     * Guarda o actualiza la reseña del usuario.
     */
    public function publishReview(): void
    {
        if (!$this->requireLogin('dejar una reseña') || !$this->requireTables()) {
            return;
        }

        $body = trim($this->body);

        if ($this->rating < 1 || $this->rating > 5) {
            $this->notify(__('Elige cuántas estrellas le das.'), 'error');

            return;
        }

        if (mb_strlen($body) < 10) {
            $this->notify(__('Cuenta un poco tu experiencia (mínimo 10 caracteres) para poder guardar tu puntuación.'), 'error');

            return;
        }

        $this->runSafely(function () use ($body) {
            $existente = Reviews::userReview(Auth::id(), Product::class, $this->productId);

            $datos = [
                'rating' => $this->rating,
                'content' => mb_substr($body, 0, 1500),
                'approved' => Config::bool('auto_moderate', true),
            ];

            if ($existente) {
                $existente->update($datos);
                $mensaje = __('Tu reseña se ha actualizado.');
            } else {
                Comment::create($datos + [
                    'user_id' => Auth::id(),
                    'commentable_type' => Product::class,
                    'commentable_id' => $this->productId,
                ]);
                $mensaje = __('¡Gracias por tu reseña!');
            }

            Reviews::flush();

            $this->notify($mensaje);
        }, 'No se pudo guardar la reseña.');
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
                'commentable_id' => $this->productId,
                'parent_id' => $parent->id,
                'content' => mb_substr($body, 0, 1500),
                'approved' => Config::bool('auto_moderate', true),
            ]);

            $this->replyBody[$commentId] = '';
            $this->replyingTo = null;

            Reviews::flush();
        }, 'No se pudo publicar la respuesta.');
    }

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

            $propia = $comment->user_id === $user->id && $comment->parent_id === null;

            $comment->delete();

            Reviews::flush();

            if ($propia) {
                $this->rating = 0;
                $this->body = '';
            }
        });
    }
}
