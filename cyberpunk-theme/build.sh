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

# --------------------- extensión + tema, SIN assets (ligera, para subidores estrictos)
mkdir -p build-tmp/lite
cp -r CyberpunkTheme build-tmp/lite/CyberpunkTheme
cp LEEME.md build-tmp/lite/CyberpunkTheme/
chmod +x build-tmp/lite/CyberpunkTheme/install.sh
cp -r cyberpunk build-tmp/lite/CyberpunkTheme/theme

# ------------------------------------ extensión TODO EN UNO (tema + assets dentro)
mkdir -p build-tmp/full
cp -r build-tmp/lite/CyberpunkTheme build-tmp/full/CyberpunkTheme
mkdir -p build-tmp/full/CyberpunkTheme/assets
[ -d public ] && cp -r public/. build-tmp/full/CyberpunkTheme/assets/

# --------------------------------------- sólo los assets (para copiar por FTP)
mkdir -p build-tmp/assets-only
[ -d public ] && cp -r public/. build-tmp/assets-only/

# ------------------------------------------------------------- zips
rm -f cyberpunk-tema.zip cyberpunk-extension.zip cyberpunk-todo-en-uno.zip \
      cyberpunk-extension-con-tema.zip cyberpunk-assets.zip

(cd build-tmp      && zip -rq9 ../cyberpunk-tema.zip                cyberpunk-tema -x "*.DS_Store" "*__MACOSX*")
(cd build-tmp/ext  && zip -rq9 ../../cyberpunk-extension.zip        CyberpunkTheme -x "*.DS_Store" "*__MACOSX*")
(cd build-tmp/lite && zip -rq9 ../../cyberpunk-extension-con-tema.zip CyberpunkTheme -x "*.DS_Store" "*__MACOSX*")
(cd build-tmp/full && zip -rq9 ../../cyberpunk-todo-en-uno.zip      CyberpunkTheme -x "*.DS_Store" "*__MACOSX*")
(cd build-tmp/assets-only && zip -rq9 ../../cyberpunk-assets.zip    . -x "*.DS_Store" "*__MACOSX*")

rm -rf build-tmp

echo
ls -lh cyberpunk-tema.zip cyberpunk-extension.zip cyberpunk-extension-con-tema.zip \
       cyberpunk-todo-en-uno.zip cyberpunk-assets.zip
echo
for z in cyberpunk-tema cyberpunk-extension cyberpunk-extension-con-tema cyberpunk-todo-en-uno cyberpunk-assets; do
    unzip -t "$z.zip" | tail -1
done
