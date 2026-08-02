#!/usr/bin/env bash
#
# Instalador del TEMA Cyberpunk para Paymenter (Sky Ultra Plus)
# =============================================================
#
# Este script instala el tema por la vía oficial de Paymenter:
#   themes/cyberpunk  +  public/cyberpunk  +  Settings → Theme
#
# USO
#   1. Sube este ZIP a tu servidor y descomprímelo donde sea.
#   2. cd a la carpeta que se creó.
#   3. bash instalar.sh /ruta/a/paymenter
#
#      Si no pasas la ruta, el script intenta detectarla solo.
#
# La extensión (panel de personalización + comunidad) es OPCIONAL y se
# instala aparte; ver LEEME.md.

set -euo pipefail

RED=$'\033[0;31m'; GREEN=$'\033[0;32m'; YELLOW=$'\033[1;33m'; CYAN=$'\033[0;36m'; NC=$'\033[0m'

say()  { echo -e "${CYAN}==>${NC} $1"; }
ok()   { echo -e "${GREEN} ok ${NC}$1"; }
warn() { echo -e "${YELLOW} !! ${NC}$1"; }
die()  { echo -e "${RED}ERROR:${NC} $1" >&2; exit 1; }

SRC="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

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
    warn "No hay assets precompilados; tendrás que ejecutar: npm run build cyberpunk"
fi

# -------------------------------------- 4. Extensión opcional (si viene)
if [ -d "$SRC/CyberpunkTheme" ]; then
    say "Copiando la extensión a extensions/Others/CyberpunkTheme ..."
    mkdir -p "$ROOT/extensions/Others/CyberpunkTheme"
    cp -r "$SRC/CyberpunkTheme/." "$ROOT/extensions/Others/CyberpunkTheme/"
    ok "Extensión copiada (actívala en Admin → Extensions)."
fi

# ---------------------------------------------------------- 5. Permisos
say "Ajustando permisos ..."
WEBUSER=""
for u in www-data nginx apache paymenter; do
    if id -u "$u" >/dev/null 2>&1; then WEBUSER="$u"; break; fi
done

if [ -n "$WEBUSER" ]; then
    chown -R "$WEBUSER:$WEBUSER" \
        "$ROOT/themes/cyberpunk" \
        "$ROOT/public/cyberpunk" \
        2>/dev/null || warn "No pude cambiar el propietario (¿necesitas sudo?)."
    [ -d "$ROOT/extensions/Others/CyberpunkTheme" ] && \
        chown -R "$WEBUSER:$WEBUSER" "$ROOT/extensions/Others/CyberpunkTheme" 2>/dev/null || true
    ok "Propietario: $WEBUSER"
else
    warn "No detecté el usuario del servidor web; revisa los permisos a mano."
fi

find "$ROOT/themes/cyberpunk" "$ROOT/public/cyberpunk" -type d -exec chmod 755 {} \; 2>/dev/null || true
find "$ROOT/themes/cyberpunk" "$ROOT/public/cyberpunk" -type f -exec chmod 644 {} \; 2>/dev/null || true

# ------------------------------------------------ 6. Activar y limpiar
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
      || warn "No pude activarlo solo. Hazlo en Admin → Settings → Theme → Cyberpunk."

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
echo "  Tema      : $ROOT/themes/cyberpunk"
echo "  Assets    : $ROOT/public/cyberpunk"
[ -d "$ROOT/extensions/Others/CyberpunkTheme" ] && \
echo "  Extensión : $ROOT/extensions/Others/CyberpunkTheme"
echo
echo "  Comprueba: Admin → Settings → Theme  (debe poner 'cyberpunk')"
echo
echo "  Para el panel de personalización, banner, comunidad y redes:"
echo "  Admin → Extensions → instala y activa 'Cyberpunk Theme',"
echo "  luego entra en Admin → Extensions → Cyberpunk Theme."
echo
