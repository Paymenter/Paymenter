#!/usr/bin/env bash
#
# Instalador del TEMA Cyberpunk para Paymenter (Sky Ultra Plus)
# =============================================================
#
# Instala el tema por la vía oficial de Paymenter:
#   themes/cyberpunk  +  public/cyberpunk  +  Settings → Theme
#
# USO
#   bash instalar.sh /ruta/a/paymenter
#
#   Si no pasas la ruta, el script intenta detectarla solo.
#
# La extensión (panel de personalización + comunidad) va aparte:
# paquete cyberpunk-extension.zip

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
    for candidate in \
        /var/www/paymenter /var/www/html/paymenter /var/www/html \
        /home/paymenter /opt/paymenter "$SRC/.." "$SRC/../.."; do
        if [ -f "$candidate/artisan" ] && [ -d "$candidate/themes" ]; then
            ROOT="$(cd "$candidate" && pwd)"
            break
        fi
    done
fi

[ -n "$ROOT" ] || die "No encontré Paymenter. Ejecuta: bash instalar.sh /ruta/a/paymenter"
[ -f "$ROOT/artisan" ] || die "En '$ROOT' no hay un artisan. Esa no es la carpeta de Paymenter."
[ -d "$ROOT/themes" ] || die "En '$ROOT' no existe la carpeta themes/."

say "Paymenter encontrado en: $ROOT"

# ------------------------------------------------------ 2. Copiar el tema
[ -d "$SRC/cyberpunk" ] || die "Falta la carpeta 'cyberpunk' junto a este script."

say "Copiando el tema a themes/cyberpunk ..."
mkdir -p "$ROOT/themes/cyberpunk"
cp -r "$SRC/cyberpunk/." "$ROOT/themes/cyberpunk/"
ok "Tema copiado (themes/cyberpunk)."

# ------------------------------------------ 3. Copiar los assets compilados
if [ -d "$SRC/public" ]; then
    say "Copiando los assets ya compilados a public/cyberpunk ..."
    mkdir -p "$ROOT/public/cyberpunk"
    cp -r "$SRC/public/." "$ROOT/public/cyberpunk/"
    ok "Assets copiados. No hace falta ejecutar npm."
else
    warn "No hay assets precompilados; ejecuta: npm run build cyberpunk"
fi

# ---------------------------------------------------------- 4. Permisos
aplicar_permisos "$ROOT"

# ------------------------------------------------ 5. Activar y limpiar
cd "$ROOT"
PHP_BIN="${PHP_BIN:-php}"

if command -v "$PHP_BIN" >/dev/null 2>&1; then
    say "Activando el tema y limpiando cachés ..."

    "$PHP_BIN" artisan tinker --execute '
        \App\Models\Setting::updateOrCreate(
            ["key" => "theme", "settingable_type" => null, "settingable_id" => null],
            ["value" => "cyberpunk", "type" => "string", "encrypted" => false]
        );
        echo "tema activado\n";
    ' 2>/dev/null && ok "Tema Cyberpunk activado." \
      || warn "No pude activarlo solo. Hazlo en Admin → Settings → Theme → cyberpunk."

    "$PHP_BIN" artisan optimize:clear >/dev/null 2>&1 || true
    "$PHP_BIN" artisan view:clear    >/dev/null 2>&1 || true
    "$PHP_BIN" artisan cache:clear   >/dev/null 2>&1 || true
    ok "Cachés limpias."
else
    warn "No encontré PHP. Activa el tema en Admin → Settings → Theme."
fi

echo
echo -e "${GREEN}==================================================${NC}"
echo -e "${GREEN}  Tema Cyberpunk instalado${NC}"
echo -e "${GREEN}==================================================${NC}"
echo
echo "  Tema   : $ROOT/themes/cyberpunk"
echo "  Assets : $ROOT/public/cyberpunk"
echo
echo "  Comprueba en: Admin → Settings → Theme  (debe poner 'cyberpunk')"
echo
echo "  Siguiente paso (opcional pero recomendado):"
echo "  instala cyberpunk-extension.zip para tener el panel de"
echo "  personalización, la comunidad, las reseñas y los avatares."
echo
