<?php

namespace Paymenter\Extensions\Others\CyberpunkTheme\Support;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Paymenter\Extensions\Others\CyberpunkTheme\Models\Avatar;

/**
 * Avatares personalizados subidos por los usuarios.
 */
class Avatars
{
    /**
     * URL del avatar personalizado, o null si no tiene.
     */
    public static function url($user): ?string
    {
        if (!$user || !isset($user->id)) {
            return null;
        }

        $path = Cache::remember('cyberpunk.avatar.' . $user->id, now()->addMinutes(30), function () use ($user) {
            try {
                return Avatar::where('user_id', $user->id)->value('path');
            } catch (\Throwable $e) {
                return null;
            }
        });

        return $path ? Storage::url($path) : null;
    }

    /**
     * Guarda un avatar nuevo y borra el anterior.
     */
    public static function store(User $user, string $path): void
    {
        $existing = Avatar::where('user_id', $user->id)->first();

        if ($existing && $existing->path !== $path) {
            self::deleteFile($existing->path);
        }

        Avatar::updateOrCreate(['user_id' => $user->id], ['path' => $path]);

        Cache::forget('cyberpunk.avatar.' . $user->id);
    }

    /**
     * Elimina el avatar personalizado (vuelve a Gravatar).
     */
    public static function remove(User $user): void
    {
        $existing = Avatar::where('user_id', $user->id)->first();

        if ($existing) {
            self::deleteFile($existing->path);
            $existing->delete();
        }

        Cache::forget('cyberpunk.avatar.' . $user->id);
    }

    private static function deleteFile(?string $path): void
    {
        if (!$path) {
            return;
        }

        try {
            Storage::disk('public')->delete($path);
        } catch (\Throwable $e) {
            // Ignoramos errores de borrado de ficheros.
        }
    }
}
