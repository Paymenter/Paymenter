# Tema Cyberpunk para Paymenter — Sky Ultra Plus

## Lo primero: cómo se instalan los temas en Paymenter

**Paymenter NO tiene subida de temas desde el panel de administración.** Lo comprobé
en el código de tu propio repositorio:

- El único subidor de ZIP del panel es `app/Admin/Pages/Extension.php`, y sólo acepta
  **extensiones** (clases que heredan de `Extension`, `Gateway` o `Server`).
  Las mueve a `extensions/`, nunca a `themes/`.
- Los temas se leen del disco: `app/Classes/Settings.php` los lista con
  `glob(base_path('themes/*'))`, y se eligen en **Admin → Settings → Theme**.
- El comando oficial para crear temas (`php artisan app:theme:create`) sólo copia
  carpetas dentro de `themes/`.

Por eso un tema **siempre** se instala copiando archivos a `themes/<nombre>/`, tal y
como dice la documentación de Paymenter. No existe un botón "subir tema".

Este paquete respeta eso: el tema acaba en `themes/cyberpunk`.

---

## Contenido del ZIP

```
cyberpunk/          →  copiar a  themes/cyberpunk        (el tema)
public/             →  copiar a  public/cyberpunk        (CSS y JS ya compilados)
CyberpunkTheme/     →  copiar a  extensions/Others/      (extensión OPCIONAL)
instalar.sh         →  lo hace todo automáticamente
LEEME.md            →  este archivo
```

---

## Opción A — Automática (recomendada)

Sube el ZIP a tu servidor por SFTP y ejecuta:

```bash
unzip cyberpunk-theme.zip
cd cyberpunk-theme
bash instalar.sh /ruta/a/paymenter
```

Ejemplo con la ruta típica:

```bash
bash instalar.sh /var/www/paymenter
```

El script copia el tema, copia los assets compilados, ajusta permisos,
activa el tema y limpia las cachés. **No necesita Node ni npm.**

---

## Opción B — Manual (copiar y pegar)

```bash
cd /var/www/paymenter          # ← tu ruta de Paymenter

# 1) el tema
mkdir -p themes/cyberpunk
cp -r /ruta/al/zip/cyberpunk/.  themes/cyberpunk/

# 2) los estilos ya compilados
mkdir -p public/cyberpunk
cp -r /ruta/al/zip/public/.     public/cyberpunk/

# 3) la extensión (opcional: panel de personalización y comunidad)
mkdir -p extensions/Others/CyberpunkTheme
cp -r /ruta/al/zip/CyberpunkTheme/.  extensions/Others/CyberpunkTheme/

# 4) permisos
chown -R www-data:www-data themes/cyberpunk public/cyberpunk extensions/Others/CyberpunkTheme
find themes/cyberpunk public/cyberpunk -type d -exec chmod 755 {} \;
find themes/cyberpunk public/cyberpunk -type f -exec chmod 644 {} \;

# 5) limpiar cachés
php artisan optimize:clear
```

Después entra en **Admin → Settings → Theme** y elige **cyberpunk**. Guarda.

> Si tu usuario del servidor web no es `www-data`, cámbialo (en cPanel suele ser tu
> usuario de hosting; en algunos VPS es `nginx` o `apache`).

---

## Opción C — Recompilar los estilos tú mismo (no hace falta)

Los assets ya vienen compilados en `public/`. Si aun así quieres recompilarlos:

```bash
cd /var/www/paymenter
npm install
npm run build cyberpunk
```

---

## La extensión (panel de personalización + comunidad)

El **tema funciona solo**: colores, banner, marketing, contadores, stock,
avisos de facturas por servidor... todo eso se configura en
**Admin → Settings → Theme**.

La **extensión** añade encima:

- Página **Admin → Extensions → Cyberpunk Theme** con todo en pestañas
  (banner con varias diapositivas, tarjetas de marketing, páginas propias con HTML,
  paletas de colores, redes sociales, restablecer de fábrica...).
- **Comunidad**: publicaciones con fotos y vídeos, likes, comentarios y respuestas,
  con moderación desde el panel.
- **Likes y comentarios en los planes**, con etiqueta "Más popular".
- **Avatares personalizados** subidos por los usuarios.
- **Contador de visitas**.

### Instalar la extensión

Ya está copiada si usaste `instalar.sh`. Sólo falta registrarla:

**Admin → Extensions → Available Extensions → pestaña "Ready to Install"** →
busca *Cyberpunk Theme* → **Install** → activa **Enabled** y guarda.

Si prefieres el subidor de ZIP del panel, usa el archivo aparte
`CyberpunkTheme.zip` (ese sí es una extensión y el subidor lo acepta).

---

## Si el subidor del panel te da "Error durante la subida"

Ese error es del subidor de archivos de Paymenter (Livewire), no del ZIP.
El mensaje `... no se ha podido subir` es la validación `uploaded` de Laravel,
que falla cuando **PHP** no puede guardar el archivo temporal. Revisa:

```bash
# 1) ¿PHP puede escribir su carpeta temporal?
php -i | grep -E "upload_tmp_dir|open_basedir|file_uploads|upload_max_filesize|post_max_size"

# 2) carpetas que Livewire necesita
cd /var/www/paymenter
mkdir -p storage/app/livewire-tmp storage/app/extensions/uploaded
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# 3) el error real
tail -50 storage/logs/laravel.log
```

Si usas Nginx, añade dentro del bloque `server`: `client_max_body_size 20M;`

De todas formas **no necesitas el subidor**: con `instalar.sh` (Opción A) o
copiando las carpetas (Opción B) el tema queda instalado igual.

---

## Desinstalar

1. **Admin → Settings → Theme** → vuelve a `default`.
2. `rm -rf themes/cyberpunk public/cyberpunk`
3. Desinstala la extensión desde **Admin → Extensions** (borra sus tablas)
   y luego `rm -rf extensions/Others/CyberpunkTheme`.
