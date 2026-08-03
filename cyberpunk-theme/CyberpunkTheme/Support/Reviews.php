<?php

namespace Paymenter\Extensions\Others\CyberpunkTheme\Support;

use App\Models\Product;
use Illuminate\Support\Facades\Cache;
use Paymenter\Extensions\Others\CyberpunkTheme\Models\Comment;

/**
 * Reseñas con estrellas.
 *
 * Hay dos destinos posibles:
 *  - un plan concreto  → commentable_type = App\Models\Product
 *  - el servicio en general → commentable_type = Comment::GENERAL
 *
 * Una reseña siempre lleva estrellas (1-5) Y texto: sin texto no hay estrellas.
 */
class Reviews
{
    /** Valoración vacía, para no repetir el array por todas partes. */
    public const EMPTY = [
        'count' => 0,
        'average' => 0.0,
        'distribution' => [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0],
    ];

    /**
     * Valoración de un plan.
     *
     * @return array{count:int,average:float,distribution:array<int,int>}
     */
    public static function stats(int $productId): array
    {
        return self::allStats()[$productId] ?? self::EMPTY;
    }

    /**
     * Valoración de todos los planes (una sola consulta, cacheada).
     *
     * @return array<int, array{count:int,average:float,distribution:array<int,int>}>
     */
    public static function allStats(): array
    {
        return Cache::remember('cyberpunk.reviews.stats', now()->addMinutes(3), function () {
            try {
                return self::summarize(
                    Comment::reviews()
                        ->where('commentable_type', Product::class)
                        ->selectRaw('commentable_id, rating, COUNT(*) as total')
                        ->groupBy('commentable_id', 'rating')
                        ->get(),
                    'commentable_id'
                );
            } catch (\Throwable $e) {
                return [];
            }
        });
    }

    /**
     * Valoración del servicio en general.
     *
     * @return array{count:int,average:float,distribution:array<int,int>}
     */
    public static function generalStats(): array
    {
        return Cache::remember('cyberpunk.reviews.general', now()->addMinutes(3), function () {
            try {
                $rows = Comment::reviews()
                    ->general()
                    ->selectRaw('rating, COUNT(*) as total')
                    ->groupBy('rating')
                    ->get();

                $distribution = self::EMPTY['distribution'];
                $count = 0;
                $sum = 0;

                foreach ($rows as $row) {
                    $rating = (int) $row->rating;
                    $total = (int) $row->total;

                    if ($rating < 1 || $rating > 5) {
                        continue;
                    }

                    $distribution[$rating] = $total;
                    $count += $total;
                    $sum += $rating * $total;
                }

                return [
                    'count' => $count,
                    'average' => $count > 0 ? round($sum / $count, 2) : 0.0,
                    'distribution' => $distribution,
                ];
            } catch (\Throwable $e) {
                return self::EMPTY;
            }
        });
    }

    /**
     * Convierte filas "destino + estrellas + total" en un resumen por destino.
     *
     * @param  \Illuminate\Support\Collection<int, object>  $rows
     * @return array<int, array{count:int,average:float,distribution:array<int,int>}>
     */
    private static function summarize($rows, string $keyColumn): array
    {
        $stats = [];

        foreach ($rows as $row) {
            $id = (int) $row->{$keyColumn};
            $rating = (int) $row->rating;
            $total = (int) $row->total;

            if ($rating < 1 || $rating > 5) {
                continue;
            }

            if (!isset($stats[$id])) {
                $stats[$id] = self::EMPTY;
            }

            $stats[$id]['distribution'][$rating] = $total;
            $stats[$id]['count'] += $total;
            $stats[$id]['average'] += $rating * $total;
        }

        foreach ($stats as $id => $row) {
            $stats[$id]['average'] = $row['count'] > 0
                ? round($row['average'] / $row['count'], 2)
                : 0.0;
        }

        return $stats;
    }

    /**
     * Planes mejor valorados.
     *
     * Se usa una media ponderada (bayesiana) para que un plan con una sola
     * reseña de 5 estrellas no adelante a otro con veinte reseñas de 4,8.
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

            $totalReviews = array_sum(array_column($stats, 'count'));
            $globalAverage = 0.0;

            foreach ($stats as $row) {
                $globalAverage += $row['average'] * $row['count'];
            }

            $globalAverage = $totalReviews > 0 ? $globalAverage / $totalReviews : 0.0;

            // Mínimo de reseñas para que la media del plan pese de verdad.
            $minimo = 3;
            $scored = [];

            foreach ($stats as $id => $row) {
                if ($row['count'] === 0) {
                    continue;
                }

                $scored[$id] = (($row['count'] * $row['average']) + ($minimo * $globalAverage))
                    / ($row['count'] + $minimo);
            }

            arsort($scored);

            return array_slice(array_keys($scored), 0, $limit);
        });
    }

    /**
     * Reseñas elegidas por el administrador para el inicio.
     *
     * Si no ha marcado ninguna a mano, se completan con las mejores
     * (5 y 4 estrellas, las más recientes primero).
     *
     * @return \Illuminate\Support\Collection<int, Comment>
     */
    public static function featured(?int $limit = null): \Illuminate\Support\Collection
    {
        $limit = max(1, $limit ?? (int) Config::theme('featured_reviews_limit', 3));

        try {
            $elegidas = Comment::reviews()
                ->with('user')
                ->where('featured', true)
                ->orderByDesc('rating')
                ->orderByDesc('created_at')
                ->take($limit)
                ->get();

            if ($elegidas->count() >= $limit || Config::theme('featured_reviews_mode', 'mixed') === 'manual') {
                return $elegidas;
            }

            // Completamos con las mejores que no estén ya elegidas.
            $faltan = $limit - $elegidas->count();

            $mejores = Comment::reviews()
                ->with('user')
                ->where('featured', false)
                ->where('rating', '>=', 4)
                ->whereNotIn('id', $elegidas->pluck('id')->all())
                ->orderByDesc('rating')
                ->orderByDesc('created_at')
                ->take($faltan)
                ->get();

            return $elegidas->concat($mejores);
        } catch (\Throwable $e) {
            return collect();
        }
    }

    /**
     * Reseña que un usuario ya dejó sobre un destino (para poder editarla).
     */
    public static function userReview(?int $userId, string $type, int $id): ?Comment
    {
        if (!$userId) {
            return null;
        }

        try {
            return Comment::reviews()
                ->where('user_id', $userId)
                ->where('commentable_type', $type)
                ->where('commentable_id', $id)
                ->first();
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Texto corto del tipo "4,8 de 5 · 23 reseñas".
     */
    public static function summaryLabel(array $stats): string
    {
        if (($stats['count'] ?? 0) === 0) {
            return 'Sin reseñas todavía';
        }

        $media = number_format((float) $stats['average'], 1, ',', '.');
        $n = (int) $stats['count'];

        return $media . ' de 5 · ' . $n . ' ' . ($n === 1 ? 'reseña' : 'reseñas');
    }

    public static function flush(): void
    {
        Cache::forget('cyberpunk.reviews.stats');
        Cache::forget('cyberpunk.reviews.popular');
        Cache::forget('cyberpunk.reviews.general');
    }
}
