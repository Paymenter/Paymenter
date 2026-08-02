#!/usr/bin/env bash
#
# Instalador del tema Cyberpunk para Paymenter (Sky Ultra Plus)
# --------------------------------------------------------------
# Uso:
#   1. Copia la carpeta CyberpunkTheme a  <paymenter>/extensions/Others/
#   2. cd <paymenter>/extensions/Others/CyberpunkTheme
#   3. bash install.sh
#
# El script:
#   - copia el tema a themes/cyberpunk
#   - copia los assets ya compilados a public/cyberpunk
#   - registra e instala la extensión (migraciones incluidas)
#   - activa el tema
#   - limpia cachés
#
# Si prefieres no usar la terminal: sube el ZIP desde
# Admin → Extensions → Available Extensions → Upload Extension.

set -euo pipefail

RED=$'\033[0;31m'; GREEN=$'\033[0;32m'; YELLOW=$'\033[1;33m'; CYAN=$'\033[0;36m'; NC=$'\033[0m'

say()  { echo -e "${CYAN}==>${NC} $1"; }
ok()   { echo -e "${GREEN} ok${NC} $1"; }
warn() { echo -e "${YELLOW} !!${NC} $1"; }
die()  { echo -e "${RED}ERROR:${NC} $1" >&2; exit 1; }

EXT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

# Buscamos la raíz de Paymenter subiendo directorios hasta encontrar artisan
ROOT="$EXT_DIR"
for _ in 1 2 3 4 5 6; do
    ROOT="$(dirname "$ROOT")"
    if [ -f "$ROOT/artisan" ] && [ -d "$ROOT/themes" ]; then
        break
    fi
done

[ -f "$ROOT/artisan" ] || die "No se encontró la instalación de Paymenter (falta artisan). Copia esta carpeta dentro de extensions/Others/ e inténtalo de nuevo."

say "Paymenter detectado en: $ROOT"

PHP_BIN="${PHP_BIN:-php}"
command -v "$PHP_BIN" >/dev/null 2>&1 || die "No se encontró PHP en el sistema."

# ---------------------------------------------------------------- 1. Tema
say "Copiando el tema a themes/cyberpunk ..."
mkdir -p "$ROOT/themes/cyberpunk"
cp -r "$EXT_DIR/theme/." "$ROOT/themes/cyberpunk/"
ok "Tema copiado."

# ------------------------------------------------------------- 2. Assets
if [ -d "$EXT_DIR/assets" ]; then
    say "Copiando los assets compilados a public/cyberpunk ..."
    mkdir -p "$ROOT/public/cyberpunk"
    cp -r "$EXT_DIR/assets/." "$ROOT/public/cyberpunk/"
    ok "Assets copiados (no hace falta compilar nada)."
else
    warn "No hay assets precompilados. Ejecuta 'npm run build cyberpunk' en $ROOT"
fi

# --------------------------------------------------- 3. Extensión en su sitio
TARGET="$ROOT/extensions/Others/CyberpunkTheme"
if [ "$EXT_DIR" != "$TARGET" ]; then
    say "Copiando la extensión a extensions/Others/CyberpunkTheme ..."
    mkdir -p "$TARGET"
    cp -r "$EXT_DIR/." "$TARGET/"
    ok "Extensión copiada."
fi

# ------------------------------------------------------------ 4. Composer
if [ ! -d "$ROOT/vendor" ]; then
    warn "No existe la carpeta vendor/. Ejecuta 'composer install --no-dev' antes de continuar."
fi

# ------------------------------------------- 5. Registro + migraciones + tema
say "Registrando la extensión y ejecutando migraciones ..."
cd "$ROOT"

"$PHP_BIN" artisan tinker --execute '
use App\Models\Extension;
use App\Helpers\ExtensionHelper;

$extension = Extension::firstOrCreate(
    ["extension" => "CyberpunkTheme", "type" => "other"],
    ["name" => "CyberpunkTheme", "enabled" => true]
);

if (! $extension->enabled) {
    $extension->update(["enabled" => true]);
}

ExtensionHelper::call($extension, "installed", mayFail: true);

echo "Extension lista\n";
' || warn "No se pudo registrar automáticamente. Actívala desde Admin → Extensions."

# --------------------------------------------------------------- 6. Cachés
say "Limpiando cachés ..."
"$PHP_BIN" artisan optimize:clear >/dev/null 2>&1 || true
"$PHP_BIN" artisan view:clear >/dev/null 2>&1 || true
"$PHP_BIN" artisan cache:clear >/dev/null 2>&1 || true
ok "Cachés limpias."

# --------------------------------------------------------------- 7. Permisos
if id -u www-data >/dev/null 2>&1; then
    say "Ajustando permisos ..."
    chown -R www-data:www-data "$ROOT/themes/cyberpunk" "$ROOT/public/cyberpunk" "$TARGET" 2>/dev/null || true
    ok "Permisos ajustados."
fi

echo
echo -e "${GREEN}=============================================${NC}"
echo -e "${GREEN} Tema Cyberpunk instalado correctamente${NC}"
echo -e "${GREEN}=============================================${NC}"
echo
echo " Siguiente paso:"
echo "   Admin → Extensions → Cyberpunk Theme"
echo "   Ahí puedes personalizar banner, marketing, colores,"
echo "   páginas nuevas, comunidad y redes sociales."
echo
echo " Si el tema no aparece activo, pulsa 'Activar tema' en esa página."
echo
