<?php

use Illuminate\Database\Migrations\Migration;
use Paymenter\Extensions\Others\CyberpunkTheme\Support\Database;

/*
 * Añade las estrellas (rating) y la marca de "destacada en el inicio"
 * (featured) a los comentarios. Como siempre, el esquema vive en
 * Support\Database: ensureTables() añade sólo lo que falte, así que esta
 * migración es segura aunque las columnas ya existan.
 */
return new class extends Migration
{
    public function up(): void
    {
        Database::ensureTables();
    }

    public function down(): void
    {
        // No se borra nada: las reseñas de los clientes se conservan.
    }
};
