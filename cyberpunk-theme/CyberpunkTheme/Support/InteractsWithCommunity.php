<?php

namespace Paymenter\Extensions\Others\CyberpunkTheme\Support;

use Illuminate\Support\Facades\Auth;

/**
 * Utilidades compartidas por los componentes Livewire de la comunidad,
 * las reseñas y los avatares.
 *
 * Evita que cualquier fallo (tablas que faltan, permisos de escritura,
 * sesión caducada) acabe en un error 500 en la cara del cliente.
 */
trait InteractsWithCommunity
{
    /** Cache por request para no consultar el esquema en cada acción */
    protected static ?bool $cyberTablesReady = null;

    /**
     * ¿Hay sesión iniciada? Si no, avisa.
     */
    protected function requireLogin(string $action = 'hacer esto'): bool
    {
        if (Auth::check()) {
            return true;
        }

        $this->notify(__('Debes iniciar sesión para :action.', ['action' => $action]), 'error');

        return false;
    }

    /**
     * ¿Están creadas las tablas de la extensión?
     */
    protected function requireTables(): bool
    {
        if (static::$cyberTablesReady === null) {
            try {
                static::$cyberTablesReady = Database::isReady();
            } catch (\Throwable $e) {
                static::$cyberTablesReady = false;
            }
        }

        if (static::$cyberTablesReady) {
            return true;
        }

        $this->notify(
            __('Esta función todavía no está lista. Un administrador debe entrar en Admin → Extensions → Cyberpunk Theme y pulsar "Reparar base de datos".'),
            'error'
        );

        return false;
    }

    /**
     * Ejecuta una acción capturando cualquier fallo y avisando al usuario
     * en vez de romper la página.
     */
    protected function runSafely(\Closure $callback, string $message = 'No se pudo completar la acción. Inténtalo de nuevo.'): mixed
    {
        try {
            return $callback();
        } catch (\Throwable $e) {
            report($e);

            static::$cyberTablesReady = null;

            $this->notify(__($message), 'error');

            return null;
        }
    }
}
