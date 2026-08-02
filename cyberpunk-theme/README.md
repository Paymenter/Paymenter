# Cyberpunk Theme para Paymenter — Sky Ultra Plus

Dos paquetes, según cómo lo quieras instalar. **Ninguno modifica el núcleo de
Paymenter ni el tema `default`.**

| Archivo | Qué es | Dónde se instala |
|---|---|---|
| `cyberpunk-theme.zip` | **El tema** (vía oficial de Paymenter) + assets compilados + la extensión + `instalar.sh` | `themes/cyberpunk`, `public/cyberpunk` y, opcional, `extensions/Others/` |
| `CyberpunkTheme.zip` | **Sólo la extensión** (lleva el tema dentro y lo copia sola) | Subidor de ZIP del panel: Admin → Extensions → Upload Extension |

## Importante: Paymenter no sube temas desde el panel

Verificado en el código de este mismo repositorio:

- `app/Admin/Pages/Extension.php` → `UploadExtensionService` es el **único** subidor
  de ZIP del panel, y sólo acepta clases que heredan de `Extension`, `Gateway` o
  `Server`; las mueve a `extensions/`, nunca a `themes/`.
- Los temas se descubren en disco: `app/Classes/Settings.php` los lista con
  `glob(base_path('themes/*'))` y se seleccionan en **Admin → Settings → Theme**.
- `php artisan app:theme:create` sólo copia carpetas dentro de `themes/`.

Por eso un tema se instala **copiando archivos a `themes/<nombre>/`**, como indica la
documentación oficial. `cyberpunk-theme.zip` hace exactamente eso.

`CyberpunkTheme.zip` existe sólo como atajo: es una extensión de verdad (por eso el
subidor la acepta) que, al instalarse, copia el tema a `themes/cyberpunk` por ti.

## Instalación recomendada

```bash
unzip cyberpunk-theme.zip
cd cyberpunk-theme
bash instalar.sh /ruta/a/paymenter
```

Instrucciones completas, instalación manual y solución de problemas del subidor:
[`LEEME.md`](LEEME.md).

Documentación del tema y de todas sus opciones:
[`CyberpunkTheme/README.md`](CyberpunkTheme/README.md).

## Regenerar los ZIP tras un cambio

```bash
# desde la raíz de Paymenter
npm run build cyberpunk

cd cyberpunk-theme
rm -rf CyberpunkTheme/theme CyberpunkTheme/assets
cp -r ../themes/cyberpunk      CyberpunkTheme/theme
cp -r ../public/cyberpunk      CyberpunkTheme/assets
zip -rq CyberpunkTheme.zip CyberpunkTheme

mkdir -p build-tmp/cyberpunk-theme
cp -r ../themes/cyberpunk build-tmp/cyberpunk-theme/cyberpunk
mkdir -p build-tmp/cyberpunk-theme/public
cp -r ../public/cyberpunk/. build-tmp/cyberpunk-theme/public/
cp -r CyberpunkTheme build-tmp/cyberpunk-theme/CyberpunkTheme
cp instalar.sh LEEME.md build-tmp/cyberpunk-theme/
(cd build-tmp && zip -rq ../cyberpunk-theme.zip cyberpunk-theme)
rm -rf build-tmp
```
