# Cyberpunk Theme para Paymenter — Sky Ultra Plus

Dos paquetes separados. **Ninguno modifica el núcleo de Paymenter ni el tema `default`.**

| Archivo | Qué es | Se instala en | Tamaño |
|---|---|---|---|
| `cyberpunk-extension.zip` | **La extensión sola** | `extensions/Others/CyberpunkTheme` | 78 KB |
| `cyberpunk-extension-con-tema.zip` | **Extensión + tema**, sin los estilos compilados | `extensions/…` y `themes/cyberpunk` | 218 KB |
| `cyberpunk-assets.zip` | **Sólo los estilos compilados** (CSS y JS) | `public/cyberpunk` | 226 KB |
| `cyberpunk-tema.zip` | **El tema completo** + estilos + `instalar.sh` | `themes/cyberpunk` y `public/cyberpunk` | 374 KB |
| `cyberpunk-todo-en-uno.zip` | **Todo**: extensión + tema + estilos | las tres carpetas de una vez | 445 KB |

El tema funciona por sí solo; la extensión añade el panel de administración y las
funciones sociales encima.

## Novedades de la 1.3.0

- **Marketing centrado de verdad.** La posición (izquierda / centro / derecha) se
  aplica a **todas** las diapositivas del banner, a los títulos de sección
  («¿Qué puedes montar con nosotros?», «Nuestros servicios», «Accesos rápidos»)
  y a las tarjetas. Las secciones con pocas tarjetas ya no quedan descolgadas.
- **Login y registro:** el lateral rota la diapositiva entera (imagen, título,
  texto e indicadores), no sólo el fondo.
- **Imágenes completas.** Nueva opción *Imágenes de categorías y productos*:
  las imágenes (por ejemplo 1080x720) se ven enteras, la caja se adapta a su
  proporción y el texto lleva un degradado detrás para que se lea siempre.
- **Comunidad estilo Facebook.** Una sola foto o vídeo se ve completo, sin
  recortes, con un fondo difuminado si es vertical; los vídeos se reproducen
  ahí mismo. Varios archivos van en rejilla.
- **Enlaces pinchables** en las publicaciones, comentarios, respuestas y reseñas.
- **Apartado de reseñas rediseñado**: resumen de me gusta y opiniones, buscador,
  orden por más valorados/comentados, miniaturas uniformes y panel de reseñas
  con la ficha del plan.
- **Selector visual de iconos** en el panel: se ven dibujados y se buscan por
  nombre entre todos los iconos disponibles.
- **Cinta de frases en movimiento desactivable** (viene desactivada).

**¿Cuál usar?**

- **Por terminal (siempre funciona):** `cyberpunk-tema.zip` + `cyberpunk-extension.zip`.
- **Todo desde el panel:** `cyberpunk-todo-en-uno.zip` (necesita que el subidor
  admita 445 KB y que el usuario web pueda escribir en `themes/` y `public/`).
- **Si el subidor rechaza archivos grandes:** sube `cyberpunk-extension-con-tema.zip`
  (218 KB) por el panel y copia `cyberpunk-assets.zip` a `public/cyberpunk/` por FTP.

Los ZIP se generan con `bash build.sh` a partir de las fuentes de esta carpeta.

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
