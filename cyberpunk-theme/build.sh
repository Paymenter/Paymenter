#!/usr/bin/env bash
#
# Regenera los dos ZIP distribuibles a partir de las fuentes de esta carpeta.
#
#   cyberpunk/        →  cyberpunk-tema.zip
#   public/           →  cyberpunk-tema.zip
#   CyberpunkTheme/   →  cyberpunk-extension.zip
#
# Uso:  bash build.sh
#
# Si has tocado las vistas o el CSS del tema, recompila antes los assets
# desde la raíz de Paymenter:
#
#   npm run build cyberpunk
#   cp -r public/cyberpunk/. cyberpunk-theme/public/

set -euo pipefail

HERE="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$HERE"

[ -d cyberpunk ]      || { echo "Falta la carpeta cyberpunk/ (el tema)"; exit 1; }
[ -d CyberpunkTheme ] || { echo "Falta la carpeta CyberpunkTheme/ (la extensión)"; exit 1; }

rm -rf build-tmp
mkdir -p build-tmp/cyberpunk-tema build-tmp/ext

# ------------------------------------------------------------ tema
cp -r cyberpunk  build-tmp/cyberpunk-tema/cyberpunk
mkdir -p build-tmp/cyberpunk-tema/public
[ -d public ] && cp -r public/. build-tmp/cyberpunk-tema/public/
cp instalar.sh LEEME.md build-tmp/cyberpunk-tema/
chmod +x build-tmp/cyberpunk-tema/instalar.sh

# ------------------------------------------------------- extensión
cp -r CyberpunkTheme build-tmp/ext/CyberpunkTheme
cp LEEME.md build-tmp/ext/CyberpunkTheme/
chmod +x build-tmp/ext/CyberpunkTheme/install.sh

# ------------------------------------------------------------- zips
rm -f cyberpunk-tema.zip cyberpunk-extension.zip
(cd build-tmp     && zip -rq ../cyberpunk-tema.zip      cyberpunk-tema  -x "*.DS_Store" "*__MACOSX*")
(cd build-tmp/ext && zip -rq ../../cyberpunk-extension.zip CyberpunkTheme -x "*.DS_Store" "*__MACOSX*")

rm -rf build-tmp

echo
ls -lh cyberpunk-tema.zip cyberpunk-extension.zip
echo
unzip -t cyberpunk-tema.zip      | tail -1
unzip -t cyberpunk-extension.zip | tail -1
