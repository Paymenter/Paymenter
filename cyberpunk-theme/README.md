# Cyberpunk Theme para Paymenter — Sky Ultra Plus

Dos paquetes separados. **Ninguno modifica el núcleo de Paymenter ni el tema `default`.**

| Archivo | Qué es | Se instala en | Tamaño |
|---|---|---|---|
| `cyberpunk-tema.zip` | **El tema** (obligatorio) + assets ya compilados + `instalar.sh` | `themes/cyberpunk` y `public/cyberpunk` | 362 KB |
| `cyberpunk-extension.zip` | **La extensión** (opcional): panel de personalización, comunidad, reseñas, avatares, visitas | `extensions/Others/CyberpunkTheme` | 58 KB |

El tema funciona por sí solo; la extensión añade el panel de administración y las
funciones sociales encima.

## Instalación rápida

```bash
# 1) el tema
unzip cyberpunk-tema.zip && cd cyberpunk-tema
bash instalar.sh /var/www/paymenter

# 2) la extensión (opcional)
cd .. && unzip cyberpunk-extension.zip && cd CyberpunkTheme
bash install.sh /var/www/paymenter
```

Instrucciones completas, instalación manual, comprobaciones y solución de
problemas: **[`LEEME.md`](LEEME.md)**.

Documentación de todas las funciones del tema:
[`CyberpunkTheme/README.md`](CyberpunkTheme/README.md).

## Paymenter no sube temas desde el panel

Verificado en el código de este repositorio:

- `app/Admin/Pages/Extension.php` → `UploadExtensionService` es el **único** subidor
  de ZIP del panel y sólo acepta clases que heredan de `Extension`, `Gateway` o
  `Server`; las mueve a `extensions/`, nunca a `themes/`.
- Los temas se descubren en disco: `app/Classes/Settings.php` los lista con
  `glob(base_path('themes/*'))` y se eligen en **Admin → Settings → Theme**.
- `php artisan app:theme:create` sólo copia carpetas dentro de `themes/`.

Por eso el tema se instala copiando archivos (`cyberpunk-tema.zip`), como indica la
documentación oficial. La extensión sí es compatible con el subidor del panel.

## Fuentes en este repositorio

```
cyberpunk-theme/
├── cyberpunk/              ← el tema        (va a themes/cyberpunk)
├── public/                 ← CSS y JS compilados (va a public/cyberpunk)
├── CyberpunkTheme/         ← la extensión   (va a extensions/Others/)
├── instalar.sh             ← instalador del tema
├── build.sh                ← regenera los dos ZIP
├── LEEME.md                ← manual de instalación
├── cyberpunk-tema.zip
└── cyberpunk-extension.zip
```

## Regenerar los ZIP tras un cambio

```bash
# si tocaste vistas o CSS del tema, recompila primero (desde la raíz de Paymenter):
cp -r cyberpunk-theme/cyberpunk themes/cyberpunk
npm run build cyberpunk
cp -r public/cyberpunk/. cyberpunk-theme/public/

# y regenera los paquetes:
bash cyberpunk-theme/build.sh
```
