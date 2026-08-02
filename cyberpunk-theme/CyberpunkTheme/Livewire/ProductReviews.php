<?php

namespace Paymenter\Extensions\Others\CyberpunkTheme\Livewire;

use App\Livewire\Component;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Paymenter\Extensions\Others\CyberpunkTheme\Support\InteractsWithCommunity;
use Paymenter\Extensions\Others\CyberpunkTheme\Models\Comment;
use Paymenter\Extensions\Others\CyberpunkTheme\Models\Like;
use Paymenter\Extensions\Others\CyberpunkTheme\Support\Config;
use Paymenter\Extensions\Others\CyberpunkTheme\Support\Reviews;

/**
 * Likes y comentarios (con respuestas) en la página de un producto/plan.
 */
class ProductReviews extends Component
{
    use InteractsWithCommunity;

    public int $productId;

    public string $body = '';

    public ?int $replyingTo = null;

    public array $replyBody = [];

    public bool $showAll = false;

    public function mount(int $productId): void
    {
        $this->productId = $productId;
    }

    public function render()
    {
        $comments = collect();
        $stats = ['likes' => 0, 'comments' => 0];
        $liked = false;

        try {
            $query = Comment::with(['user', 'replies.user'])
                ->where('commentable_type', Product::class)
                ->where('commentable_id', $this->productId)
                ->whereNull('parent_id')
                ->where('approved', true)
                ->orderByDesc('created_at');

            $comments = $this->showAll ? $query->get() : $query->take(5)->get();

            $stats = Reviews::stats($this->productId);

            $liked = Auth::check() && Like::where('user_id', Auth::id())
                ->where('likeable_type', Product::class)
                ->where('likeable_id', $this->productId)
                ->exists();
        } catch (\Throwable $e) {
            // Tablas aún no migradas.
        }

        return view('cyberpunk::livewire.product-reviews', [
            'comments' => $comments,
            'stats' => $stats,
            'liked' => $liked,
            'totalComments' => $stats['comments'],
        ]);
    }

    public function toggleLike(): void
    {
        if (!$this->requireLogin('comentar') || !$this->requireTables()) {
            return;
        }

        $like = Like::where('user_id', Auth::id())
            ->where('likeable_type', Product::class)
            ->where('likeable_id', $this->productId)
            ->first();

        if ($like) {
            $like->delete();
        } else {
            Like::create([
                'user_id' => Auth::id(),
                'likeable_type' => Product::class,
                'likeable_id' => $this->productId,
            ]);
        }

        Reviews::flush();
    }

    public function comment(): void
    {
        if (!$this->requireLogin('comentar') || !$this->requireTables()) {
            return;
        }

        $body = trim($this->body);

        if (mb_strlen($body) < 3) {
            $this->notify(__('Escribe un comentario un poco más largo.'), 'error');

            return;
        }

        Comment::create([
            'user_id' => Auth::id(),
            'commentable_type' => Product::class,
            'commentable_id' => $this->productId,
            'content' => mb_substr($body, 0, 1500),
            'approved' => Config::bool('auto_moderate', true),
        ]);

        $this->body = '';
        Reviews::flush();

        $this->notify(__('¡Gracias por tu comentario!'));
    }

    public function reply(int $commentId): void
    {
        if (!$this->requireLogin('comentar') || !$this->requireTables()) {
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
            'commentable_type' => Product::class,
            'commentable_id' => $this->productId,
            'parent_id' => $parent->id,
            'content' => mb_substr($body, 0, 1500),
            'approved' => Config::bool('auto_moderate', true),
        ]);

        $this->replyBody[$commentId] = '';
        $this->replyingTo = null;

        Reviews::flush();
    }

    public function toggleCommentLike(int $commentId): void
    {
        if (!$this->requireLogin('comentar') || !$this->requireTables()) {
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

    public function deleteComment(int $commentId): void
    {
        $comment = Comment::find($commentId);

        if (!$comment || !Auth::check()) {
            return;
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->id !== $comment->user_id && $user->role_id === null) {
            return;
        }

        $comment->delete();

        Reviews::flush();
    }
}
