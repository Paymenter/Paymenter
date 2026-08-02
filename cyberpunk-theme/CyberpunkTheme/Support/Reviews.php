<?php

namespace Paymenter\Extensions\Others\CyberpunkTheme\Support;

use App\Models\Product;
use Illuminate\Support\Facades\Cache;
use Paymenter\Extensions\Others\CyberpunkTheme\Models\Comment;
use Paymenter\Extensions\Others\CyberpunkTheme\Models\Like;

/**
 * Likes y comentarios sobre productos/planes, y cálculo del "Más popular".
 */
class Reviews
{
    /**
     * Likes y comentarios de un producto.
     *
     * @return array{likes:int,comments:int}
     */
    public static function stats(int $productId): array
    {
        $all = self::allStats();

        return $all[$productId] ?? ['likes' => 0, 'comments' => 0];
    }

    /**
     * Estadísticas de todos los productos (una sola consulta, cacheada).
     */
    public static function allStats(): array
    {
        return Cache::remember('cyberpunk.reviews.stats', now()->addMinutes(3), function () {
            $stats = [];

            try {
                $likes = Like::where('likeable_type', Product::class)
                    ->selectRaw('likeable_id, COUNT(*) as total')
                    ->groupBy('likeable_id')
                    ->pluck('total', 'likeable_id');

                $comments = Comment::where('commentable_type', Product::class)
                    ->where('approved', true)
                    ->selectRaw('commentable_id, COUNT(*) as total')
                    ->groupBy('commentable_id')
                    ->pluck('total', 'commentable_id');

                foreach ($likes as $id => $total) {
                    $stats[(int) $id]['likes'] = (int) $total;
                }

                foreach ($comments as $id => $total) {
                    $stats[(int) $id]['comments'] = (int) $total;
                }

                foreach ($stats as $id => $row) {
                    $stats[$id] = [
                        'likes' => $row['likes'] ?? 0,
                        'comments' => $row['comments'] ?? 0,
                    ];
                }
            } catch (\Throwable $e) {
                return [];
            }

            return $stats;
        });
    }

    /**
     * IDs de los productos más populares (likes + comentarios).
     *
     * @return array<int, int>
     */
    public static function popularProductIds(int $limit = 3): array
    {
        return Cache::remember('cyberpunk.reviews.popular', now()->addMinutes(3), function () use ($limit) {
            $stats = self::allStats();

            if (count($stats) === 0) {
                return [];
            }

            $scored = [];
            foreach ($stats as $id => $row) {
                $score = ($row['likes'] * 2) + $row['comments'];
                if ($score > 0) {
                    $scored[$id] = $score;
                }
            }

            arsort($scored);

            return array_slice(array_keys($scored), 0, $limit);
        });
    }

    public static function flush(): void
    {
        Cache::forget('cyberpunk.reviews.stats');
        Cache::forget('cyberpunk.reviews.popular');
    }
}
