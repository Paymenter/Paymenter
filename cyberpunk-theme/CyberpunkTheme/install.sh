#!/usr/bin/env bash
#
# Instalador de la EXTENSIÓN Cyberpunk Theme para Paymenter (Sky Ultra Plus)
# =========================================================================
#
# La extensión añade al tema:
#   - panel de personalización (Admin → Extensions → Cyberpunk Theme)
#   - comunidad (publicaciones con fotos/vídeos, likes, comentarios)
#   - likes y comentarios en los planes, con etiqueta "Más popular"
#   - avatares personalizados y contador de visitas
#
# REQUISITO: instala antes el tema (paquete cyberpunk-tema.zip).
#
# USO
#   bash install.sh /ruta/a/paymenter
#
#   Si no pasas la ruta, el script intenta detectarla solo.

set -euo pipefail

RED=$'\033[0;31m'; GREEN=$'\033[0;32m'; YELLOW=$'\033[1;33m'; CYAN=$'\033[0;36m'; NC=$'\033[0m'

say()  { echo -e "${CYAN}==>${NC} $1"; }
ok()   { echo -e "${GREEN} ok ${NC}$1"; }
warn() { echo -e "${YELLOW} !! ${NC}$1"; }
die()  { echo -e "${RED}ERROR:${NC} $1" >&2; exit 1; }

SRC="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

# ---------------------------------------------------------- Permisos
# Detecta el usuario del servidor web y aplica TODOS los permisos que
# necesitan el tema, la extensión y las subidas de los usuarios.
detectar_webuser() {
    local u
    for u in www-data nginx apache http paymenter; do
        if id -u "$u" >/dev/null 2>&1; then echo "$u"; return; fi
    done
    # cPanel / hosting compartido: el dueño de la carpeta
    stat -c '%U' "$1" 2>/dev/null || echo ""
}

aplicar_permisos() {
    local ROOT="$1"
    local WEBUSER
    WEBUSER="$(detectar_webuser "$ROOT")"

    if [ -z "$WEBUSER" ]; then
        warn "No detecté el usuario del servidor web; revisa los permisos a mano."
        return
    fi

    local GRP
    GRP="$(id -gn "$WEBUSER" 2>/dev/null || echo "$WEBUSER")"

    say "Aplicando permisos para $WEBUSER:$GRP ..."

    for d in \
        "$ROOT/themes/cyberpunk" \
        "$ROOT/public/cyberpunk" \
        "$ROOT/extensions/Others/CyberpunkTheme" \
        "$ROOT/storage" \
        "$ROOT/bootstrap/cache" \
        "$ROOT/public/storage"; do
        [ -e "$d" ] && chown -R "$WEBUSER:$GRP" "$d" 2>/dev/null || true
    done

    # Carpetas donde la web escribe (avatares, fotos de la comunidad,
    # banners, subidas temporales de Livewire y de extensiones)
    for d in \
        "$ROOT/storage/app/public/cyberpunk" \
        "$ROOT/storage/app/public/cyberpunk/avatars" \
        "$ROOT/storage/app/public/cyberpunk/community" \
        "$ROOT/storage/app/public/cyberpunk/banner" \
        "$ROOT/storage/app/public/cyberpunk/backgrounds" \
        "$ROOT/storage/app/livewire-tmp" \
        "$ROOT/storage/app/extensions/uploaded"; do
        mkdir -p "$d" 2>/dev/null || true
        chown -R "$WEBUSER:$GRP" "$d" 2>/dev/null || true
        chmod -R 775 "$d" 2>/dev/null || true
    done

    # Lectura para el tema y los assets; escritura para storage y caché
    find "$ROOT/themes/cyberpunk" "$ROOT/public/cyberpunk" -type d -exec chmod 755 {} \; 2>/dev/null || true
    find "$ROOT/themes/cyberpunk" "$ROOT/public/cyberpunk" -type f -exec chmod 644 {} \; 2>/dev/null || true
    [ -d "$ROOT/extensions/Others/CyberpunkTheme" ] && {
        find "$ROOT/extensions/Others/CyberpunkTheme" -type d -exec chmod 755 {} \; 2>/dev/null || true
        find "$ROOT/extensions/Others/CyberpunkTheme" -type f -exec chmod 644 {} \; 2>/dev/null || true
        chmod +x "$ROOT/extensions/Others/CyberpunkTheme/install.sh" 2>/dev/null || true
    }
    chmod -R 775 "$ROOT/storage" "$ROOT/bootstrap/cache" 2>/dev/null || true

    # Enlace público de storage (necesario para ver avatares e imágenes)
    if [ ! -e "$ROOT/public/storage" ]; then
        (cd "$ROOT" && php artisan storage:link >/dev/null 2>&1) || true
    fi

    ok "Permisos aplicados."
}


# ------------------------------------------------ 1. Localizar Paymenter
ROOT="${1:-}"

if [ -z "$ROOT" ]; then
    # ¿Ya estamos dentro de extensions/Others/CyberpunkTheme?
    CANDIDATE="$SRC"
    for _ in 1 2 3 4; do
        CANDIDATE="$(dirname "$CANDIDATE")"
        if [ -f "$CANDIDATE/artisan" ] && [ -d "$CANDIDATE/themes" ]; then
            ROOT="$CANDIDATE"
            break
        fi
    done
fi

if [ -z "$ROOT" ]; then
    for candidate in /var/www/paymenter /var/www/html/paymenter /var/www/html /home/paymenter /opt/paymenter; do
        if [ -f "$candidate/artisan" ] && [ -d "$candidate/themes" ]; then
            ROOT="$(cd "$candidate" && pwd)"
            break
        fi
    done
fi

[ -n "$ROOT" ] || die "No encontré Paymenter. Ejecuta: bash install.sh /ruta/a/paymenter"
[ -f "$ROOT/artisan" ] || die "En '$ROOT' no hay un artisan. Esa no es la carpeta de Paymenter."

say "Paymenter encontrado en: $ROOT"

if [ ! -d "$ROOT/themes/cyberpunk" ]; then
    warn "No existe themes/cyberpunk. Instala primero el tema (cyberpunk-tema.zip)."
    warn "La extensión se instalará igual, pero el tema no se podrá activar."
fi

# --------------------------------------------- 2. Copiar la extensión
TARGET="$ROOT/extensions/Others/CyberpunkTheme"

if [ "$SRC" != "$TARGET" ]; then
    say "Copiando la extensión a extensions/Others/CyberpunkTheme ..."
    mkdir -p "$TARGET"
    cp -r "$SRC/." "$TARGET/"
    ok "Extensión copiada."
else
    ok "La extensión ya está en su sitio."
fi

# ----------------------------- 3. Tema y assets incluidos (si los trae)
if [ -d "$SRC/theme" ]; then
    say "Este paquete también trae el tema; copiándolo a themes/cyberpunk ..."
    mkdir -p "$ROOT/themes/cyberpunk"
    cp -r "$SRC/theme/." "$ROOT/themes/cyberpunk/"
    ok "Tema copiado."
fi

if [ -d "$SRC/assets" ]; then
    say "Copiando assets compilados a public/cyberpunk ..."
    mkdir -p "$ROOT/public/cyberpunk"
    cp -r "$SRC/assets/." "$ROOT/public/cyberpunk/"
    ok "Assets copiados."
fi

# ---------------------------------------------------------- 4. Permisos
aplicar_permisos "$ROOT"

# ------------------------------------ 5. Registrar, migrar y activar
cd "$ROOT"
PHP_BIN="${PHP_BIN:-php}"

command -v "$PHP_BIN" >/dev/null 2>&1 || die "No encontré PHP. Instala la extensión desde Admin → Extensions."

[ -d "$ROOT/vendor" ] || warn "No existe vendor/. Ejecuta 'composer install --no-dev' antes de continuar."

say "Registrando la extensión y ejecutando migraciones ..."

"$PHP_BIN" artisan tinker --execute '
use App\Helpers\ExtensionHelper;
use App\Models\Extension;

$extension = Extension::firstOrCreate(
    ["extension" => "CyberpunkTheme", "type" => "other"],
    ["name" => "CyberpunkTheme", "enabled" => true]
);

if (! $extension->enabled) {
    $extension->update(["enabled" => true]);
}

ExtensionHelper::call($extension, "installed", mayFail: true);

echo "extension registrada y activada\n";
' || warn "No se pudo registrar sola. Hazlo en Admin → Extensions → Ready to Install."

say "Limpiando cachés ..."
"$PHP_BIN" artisan optimize:clear >/dev/null 2>&1 || true
"$PHP_BIN" artisan view:clear     >/dev/null 2>&1 || true
"$PHP_BIN" artisan cache:clear    >/dev/null 2>&1 || true
ok "Cachés limpias."

echo
echo -e "${GREEN}==================================================${NC}"
echo -e "${GREEN}  Extensión Cyberpunk Theme instalada${NC}"
echo -e "${GREEN}==================================================${NC}"
echo
echo "  Ruta: $TARGET"
echo
echo "  Ahora entra en:"
echo "    Admin → Extensions → Cyberpunk Theme"
echo
echo "  Ahí configuras banner, marketing, colores, páginas nuevas,"
echo "  comunidad y redes sociales."
echo
echo "  Si no aparece, ve a Admin → Extensions → Extensions y"
echo "  comprueba que 'CyberpunkTheme' está en Enabled."
echo
