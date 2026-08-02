<?php

use Illuminate\Database\Migrations\Migration;
use Paymenter\Extensions\Others\CyberpunkTheme\Support\Database;

/*
 * El esquema vive en Support\Database para que las migraciones, la
 * instalación y el botón "Reparar base de datos" del panel usen exactamente
 * el mismo código. ensureTables() crea sólo las tablas que falten, así que
 * es seguro ejecutarlo tantas veces como haga falta.
 */
return new class extends Migration
{
    public function up(): void
    {
        Database::ensureTables();
    }

    public function down(): void
    {
        Database::dropTables();
    }
};
