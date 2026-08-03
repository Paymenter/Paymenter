# Tema Cyberpunk para Paymenter — Sky Ultra Plus

Paquetes disponibles (elige según lo que admita tu servidor):

| Paquete | Qué instala | Dónde | Tamaño |
|---|---|---|---|
| `cyberpunk-extension.zip` | La extensión sola | `extensions/Others/CyberpunkTheme` | 82 KB |
| `cyberpunk-extension-con-tema.zip` | Extensión + tema (sin estilos) | `extensions/…` + `themes/cyberpunk` | 222 KB |
| `cyberpunk-assets.zip` | Sólo los estilos compilados | `public/cyberpunk` | 226 KB |
| `cyberpunk-tema.zip` | El tema completo + estilos | `themes/cyberpunk` + `public/cyberpunk` | 375 KB |
| `cyberpunk-todo-en-uno.zip` | Todo de una vez | las tres carpetas | 448 KB |

## Si el subidor del panel rechaza los archivos grandes

Si `cyberpunk-extension.zip` (82 KB) sube pero `cyberpunk-todo-en-uno.zip` (448 KB)
no, tu servidor tiene un límite de subida bajo. Compruébalo:

```bash
php -i | grep -E "upload_max_filesize|post_max_size|memory_limit"
grep -r client_max_body_size /etc/nginx/ 2>/dev/null
```

Súbelo (por ejemplo a 20M) en tu `php.ini` y, si usas Nginx, añade
`client_max_body_size 20M;` dentro del bloque `server`. Después reinicia
PHP-FPM y Nginx.

Mientras tanto, la combinación que funciona con límites bajos es:

1. Sube por el panel `cyberpunk-extension-con-tema.zip` (222 KB) → instala la
   extensión **y** el tema.
2. Copia por FTP el contenido de `cyberpunk-assets.zip` a `public/cyberpunk/`.

> El tema **no se activa solo** si faltan los estilos: se quedaría la tienda sin
> diseño. En cuanto copies los assets, entra en **Admin → Extensions → Cyberpunk
> Theme** y pulsa **Activar tema**.

El **tema funciona solo**. La **extensión** añade el panel de personalización,
la comunidad, las reseñas en los planes, los avatares y el contador de visitas.

## ¿La extensión puede instalar el tema por mí?

**Sí, pero sólo el paquete `cyberpunk-todo-en-uno.zip`**, que lleva el tema dentro.
`cyberpunk-extension.zip` es la extensión pelada y **no** trae el tema.

Con el todo-en-uno, al instalar la extensión se copia el tema a `themes/cyberpunk`,
los assets a `public/cyberpunk` y se activa el tema automáticamente.

Requisito: el usuario del servidor web tiene que poder escribir en `themes/` y en
`public/`. Compruébalo así:

```bash
cd /var/www/paymenter
sudo -u www-data test -w themes  && echo "themes: OK" || echo "themes: SIN PERMISO"
sudo -u www-data test -w public  && echo "public: OK" || echo "public: SIN PERMISO"
```

Si sale «SIN PERMISO», dale permiso sólo a esas dos carpetas:

```bash
chown www-data:www-data themes public
chmod 775 themes public
```

O simplemente instala el tema por terminal con `cyberpunk-tema.zip` (más abajo);
el resultado es idéntico.

Si la extensión no pudo copiar el tema, te lo dirá en
**Admin → Extensions → Cyberpunk Theme**: verás el aviso «El tema Cyberpunk todavía
no está activo» o «Faltan los assets compilados», y el botón **Reinstalar archivos**
vuelve a intentarlo.

---

## Por qué el tema no se sube desde el panel

Paymenter **no tiene subidor de temas**. Verificado en el código:

- `app/Admin/Pages/Extension.php` → `UploadExtensionService` es el único subidor de
  ZIP del panel y sólo acepta **extensiones**; las mueve a `extensions/`, nunca a
  `themes/`.
- Los temas se leen del disco: `app/Classes/Settings.php` los lista con
  `glob(base_path('themes/*'))` y se eligen en **Admin → Settings → Theme**.

Por eso los temas se instalan **copiando archivos**, como dice la documentación
oficial. Eso es justo lo que hace este paquete.

---

# 1) Instalar el TEMA

## Automático

```bash
cd /tmp
unzip cyberpunk-tema.zip
cd cyberpunk-tema
bash instalar.sh /var/www/paymenter        # ← pon TU ruta de Paymenter
```

El script copia el tema y los assets, ajusta permisos, activa el tema y limpia
las cachés. **No necesita Node ni npm.**

## Manual

```bash
PAYMENTER=/var/www/paymenter               # ← pon TU ruta

cd /tmp
unzip cyberpunk-tema.zip

# el tema
mkdir -p "$PAYMENTER/themes/cyberpunk"
cp -r cyberpunk-tema/cyberpunk/.  "$PAYMENTER/themes/cyberpunk/"

# los estilos y scripts ya compilados
mkdir -p "$PAYMENTER/public/cyberpunk"
cp -r cyberpunk-tema/public/.     "$PAYMENTER/public/cyberpunk/"

# permisos (cambia www-data por tu usuario web si es otro)
chown -R www-data:www-data "$PAYMENTER/themes/cyberpunk" "$PAYMENTER/public/cyberpunk"
find "$PAYMENTER/themes/cyberpunk" "$PAYMENTER/public/cyberpunk" -type d -exec chmod 755 {} \;
find "$PAYMENTER/themes/cyberpunk" "$PAYMENTER/public/cyberpunk" -type f -exec chmod 644 {} \;

# limpiar cachés
cd "$PAYMENTER"
php artisan optimize:clear
```

Después: **Admin → Settings → Theme** → elige **cyberpunk** → Guardar.

---

# 2) Instalar la EXTENSIÓN (opcional)

## Automático

```bash
cd /tmp
unzip cyberpunk-extension.zip
cd CyberpunkTheme
bash install.sh /var/www/paymenter         # ← pon TU ruta
```

Copia la extensión, la registra, ejecuta las migraciones, la deja activada y
limpia las cachés.

## Manual

```bash
PAYMENTER=/var/www/paymenter               # ← pon TU ruta

cd /tmp
unzip cyberpunk-extension.zip

mkdir -p "$PAYMENTER/extensions/Others/CyberpunkTheme"
cp -r CyberpunkTheme/.  "$PAYMENTER/extensions/Others/CyberpunkTheme/"

chown -R www-data:www-data "$PAYMENTER/extensions/Others/CyberpunkTheme"
find "$PAYMENTER/extensions/Others/CyberpunkTheme" -type d -exec chmod 755 {} \;
find "$PAYMENTER/extensions/Others/CyberpunkTheme" -type f -exec chmod 644 {} \;

cd "$PAYMENTER"
php artisan optimize:clear
```

Después, en el panel: **Admin → Extensions → Available Extensions →
pestaña "Ready to Install"** → busca *Cyberpunk Theme* → **Install** →
activa **Enabled** y guarda.

> La extensión **sí** se puede subir por **Admin → Extensions → Upload Extension**
> con el ZIP `cyberpunk-extension.zip`, porque es una extensión de verdad.

---

# 3) Dónde se configura todo

**Admin → Extensions → Cyberpunk Theme**

| Pestaña | Qué controla |
|---|---|
| General | Qué bloques se ven en el inicio, cinta de frases, cómo se ven las imágenes, opciones de tienda, textos |
| Apariencia | 8 paletas de colores, colores manuales, animaciones de fondo por modo, efectos neón/scanlines/glitch, tipografía, imagen de fondo |
| Banner | Diapositivas (imagen, título, texto, botón) y frases de marketing en movimiento |
| Marketing | Posición (izquierda/centro/derecha), tarjetas de servicios, ventajas y accesos rápidos, con **selector visual de iconos** |
| Páginas | Páginas nuevas con tu HTML, con enlace en la barra de navegación |
| Comunidad | Nombre, URL, descripción, límite de archivos, avatares y el apartado de reseñas |
| Redes sociales | Facebook, Discord, Instagram, canal y grupo de WhatsApp, Telegram, YouTube, TikTok, X, GitHub |

Botones de arriba: **Guardar cambios**, **Activar tema**, **Reinstalar archivos**,
**Reparar base de datos**, **Reiniciar visitas** y **Restablecer todo**
(vuelve a la configuración de fábrica).

### Ajustes que quizá busques

- **Cinta de frases en movimiento** (General): la tira que va pasando frases bajo
  el banner. Viene **desactivada**; actívala si la quieres.
- **Imágenes de categorías y productos** (General):
  - *Completas* (por defecto) — se ve toda la imagen y la caja se adapta a su
    tamaño, con un degradado detrás del texto para que se lea bien. Pensado para
    imágenes tipo 1080x720.
  - *Recortadas* — todas a la misma altura, como una miniatura.
- **Posición del marketing** (Marketing): coloca a la izquierda, en el centro o a
  la derecha el banner, los títulos de sección y las tarjetas.
- **Selector de iconos**: en las tarjetas, ventajas, accesos rápidos y páginas el
  icono se elige de una lista donde se ve dibujado. Escribe en inglés para buscar
  entre todos los iconos disponibles (`server`, `cloud`, `robot`, `whatsapp`...).

Moderación: **Admin → Extensions → Comunidad · Publicaciones** y
**Comunidad · Comentarios**.

Sin la extensión, los ajustes básicos del tema (colores, textos, efectos, redes)
están en **Admin → Settings → Theme**.

---

# 4) Comprobar que funciona

```bash
cd /var/www/paymenter

# el tema está en su sitio
ls themes/cyberpunk/theme.php
ls public/cyberpunk/manifest.json

# el tema está activo
php artisan tinker --execute 'echo config("settings.theme");'   # debe imprimir: cyberpunk
```

Si la web sale sin estilos, es que falta `public/cyberpunk/` — vuelve a copiarlo
o compílalo con `npm run build cyberpunk`.

---

# 5) Problemas frecuentes

**La web sale en blanco o sin estilos**
```bash
cd /var/www/paymenter
php artisan optimize:clear
php artisan view:clear
ls public/cyberpunk/manifest.json     # tiene que existir
```

**Error 500 después de instalar**
```bash
tail -50 storage/logs/laravel.log
```

**No aparece "cyberpunk" en Settings → Theme**
La carpeta `themes/cyberpunk` no existe o el servidor web no puede leerla:
```bash
ls -la themes/
chown -R www-data:www-data themes/cyberpunk
```

**"Error durante la subida" al subir la extensión por el panel**
Es un problema de PHP en tu servidor, no del ZIP: falla la validación `uploaded`
de Laravel porque PHP no puede escribir su archivo temporal.
```bash
php -i | grep -E "upload_tmp_dir|open_basedir|file_uploads|upload_max_filesize|post_max_size"
mkdir -p storage/app/livewire-tmp storage/app/extensions/uploaded
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
tail -50 storage/logs/laravel.log
```
En Nginx añade dentro del bloque `server`: `client_max_body_size 20M;`
De todas formas, con `install.sh` no necesitas el subidor.

**En la comunidad no me deja subir varias fotos, o los vídeos fallan**

El tema sube los archivos **de uno en uno**, así que el problema no es el
número de archivos sino lo que pesa **cada uno**. Mira tus límites:

```bash
php -i | grep -E "upload_max_filesize|post_max_size|max_file_uploads"
```

El formulario te dice el máximo real («hasta 2 MB cada uno»), y si un archivo
no cabe te avisa por su nombre en vez de fallar en silencio. Para permitir
fotos y vídeos grandes, sube estos valores en tu `php.ini`:

```ini
upload_max_filesize = 32M
post_max_size = 40M
max_file_uploads = 20
```

Y en Nginx, dentro del bloque `server`: `client_max_body_size 40M;`
Después reinicia PHP-FPM y Nginx. El máximo que acepta el tema por archivo son
20 MB; el número de archivos por publicación se cambia en
**Admin → Extensions → Cyberpunk Theme → Comunidad**.

---

# 6) Recompilar los estilos (no hace falta)

Los assets vienen compilados. Si cambias las vistas del tema:

```bash
cd /var/www/paymenter
npm install
npm run build cyberpunk
```

---

# 7) Actualizar a una versión nueva

**No se pierde nada.** Ni la configuración, ni las publicaciones de la
comunidad, ni los comentarios, los me gusta, los avatares o los contadores.

Sólo hay que instalar encima, igual que la primera vez:

```bash
cd /tmp
unzip -o cyberpunk-tema.zip && cd cyberpunk-tema
bash instalar.sh /var/www/paymenter

cd /tmp && unzip -o cyberpunk-extension.zip && cd CyberpunkTheme
bash install.sh /var/www/paymenter
```

Por qué no se borra nada:

- Los archivos se copian **encima** de los que ya hay; nunca se borra la carpeta
  del tema ni la de la extensión.
- Los ajustes sólo se **añaden**: una versión nueva crea las claves que le
  falten y respeta todo lo que ya tengas configurado.
- Desinstalar la extensión **ya no borra sus tablas**, así que actualizar
  desde el panel (desinstalar la vieja → instalar la nueva) conserva la
  comunidad entera.

Lo único que borra de verdad es el botón **Restablecer todo** del panel, y
avisa antes. Aun así, ese botón tampoco toca las publicaciones.

Si actualizas desde el panel con el ZIP, entra después en
**Admin → Extensions → Cyberpunk Theme** y pulsa **Reinstalar archivos** para
copiar el tema y los estilos nuevos.

---

# 8) Desinstalar

```bash
cd /var/www/paymenter

# 1. volver al tema por defecto (o hazlo en Admin → Settings → Theme)
php artisan tinker --execute '\App\Models\Setting::where("key","theme")->update(["value"=>"default"]);'

# 2. borrar el tema
rm -rf themes/cyberpunk public/cyberpunk

# 3. la extensión: desinstálala desde Admin → Extensions y luego:
rm -rf extensions/Others/CyberpunkTheme

php artisan optimize:clear
```

> Desinstalar **no borra** las publicaciones de la comunidad: se quedan en las
> tablas `ext_cyberpunk_*` por si vuelves a instalarla. Si de verdad quieres
> eliminarlas, hay que borrar esas tablas a mano en la base de datos.
