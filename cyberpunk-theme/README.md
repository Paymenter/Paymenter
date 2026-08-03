# Cyberpunk Theme para Paymenter — Sky Ultra Plus

Dos paquetes separados. **Ninguno modifica el núcleo de Paymenter ni el tema `default`.**

| Archivo | Qué es | Se instala en | Tamaño |
|---|---|---|---|
| `cyberpunk-extension.zip` | **La extensión sola** | `extensions/Others/CyberpunkTheme` | 89 KB |
| `cyberpunk-extension-con-tema.zip` | **Extensión + tema**, sin los estilos compilados | `extensions/…` y `themes/cyberpunk` | 239 KB |
| `cyberpunk-assets.zip` | **Sólo los estilos compilados** (CSS y JS) | `public/cyberpunk` | 227 KB |
| `cyberpunk-tema.zip` | **El tema completo** + estilos + `instalar.sh` | `themes/cyberpunk` y `public/cyberpunk` | 385 KB |
| `cyberpunk-todo-en-uno.zip` | **Todo**: extensión + tema + estilos | las tres carpetas de una vez | 465 KB |

El tema funciona por sí solo; la extensión añade el panel de administración y las
funciones sociales encima.

## Novedades de la 1.4.1

- **Vuelve la insignia de categoría**, ahora como «La mejor valorada» y
  calculada con las estrellas de sus productos. Había desaparecido porque el
  cálculo seguía leyendo los «me gusta», que dejaron de existir en la 1.4.0:
  en producción eso lanzaba una excepción y el resultado acababa siendo
  «ninguna categoría».
- **Las visitas ya no se pierden.** El total se guarda aparte, en la tabla de
  ajustes, además del detalle por días. Aunque se vacíe la tabla de días, se
  limpie la caché, se reinicie el servidor o se reinstale la extensión, el
  total nunca baja. Sólo lo pone a cero el botón **Reiniciar visitas**.
- Para no contar dos veces al mismo visitante, además de la sesión se usa una
  cookie: si el servidor se reinicia y se pierden las sesiones, el conteo del
  día sigue siendo correcto.
- **El tiempo activo ya no se reinicia.** La fecha de arranque se fija una vez
  y se guarda; antes se recalculaba a partir de la factura o el usuario más
  antiguo, así que borrar ese registro movía el contador. Se puede poner la
  fecha real de apertura en *General → Contadores*.
- «Restablecer todo» ya no toca el contador de visitas ni la fecha de arranque.

## Novedades de la 1.4.0

- **Reseñas por estrellas.** El «me gusta» de los planes pasa a ser una
  valoración de 1 a 5 estrellas, con nota media, reparto de estrellas y la
  puntuación de cada cliente junto a su comentario.
- **Sin reseña no hay estrellas.** Para puntuar hay que escribir: el botón de
  publicar sólo se activa con estrellas **y** texto, y se ve en todo momento
  qué falta. Cada cliente deja una reseña por plan, y puede actualizarla.
- **Dos apartados de reseñas**: las de cada plan y la opinión sobre el
  **servicio en general** (soporte, velocidad, estabilidad). El nombre y la
  descripción de la segunda se cambian desde el panel.
- **Reseñas destacadas en el inicio.** Desde *Cyberpunk Theme → Comunidad →
  Reseñas destacadas* eliges a mano las mejores (de un plan o del servicio) y
  se muestran en la página principal **después de los planes y justo antes de
  la comunidad**, con la nota media del hosting.
- El moderador de comentarios enseña las estrellas, permite **destacar o
  quitar** una reseña con un clic y filtrar por destacadas o por origen
  (comunidad / plan / servicio).
- La insignia de los planes pasa a ser **«Mejor valorado»**, calculada con una
  media ponderada para que un plan con una sola reseña de 5 no adelante a otro
  con veinte reseñas de 4,8.

## Novedades de la 1.3.1

- **Actualizar ya no borra nada.** Desinstalar la extensión (lo que hace el
  panel al actualizar) ya **no** hace rollback de las migraciones, así que las
  publicaciones, comentarios, me gusta, avatares y contadores sobreviven a
  cualquier actualización. Los ajustes tampoco se tocan: sólo se añaden las
  claves nuevas.
- **Subida de varias fotos y vídeos arreglada.** Los archivos se mandaban todos
  en la misma petición, así que en servidores con `post_max_size` bajo se caía
  la petición entera y no subía casi nada. Ahora **cada archivo va en su propia
  petición**: se pueden adjuntar los 4 (o los que configures, hasta 10),
  mezclando fotos y vídeos.
  Verificado con `post_max_size = 8M` y 6 fotos de 1,6 MB: antes subían 0,
  ahora suben las 6.
- El formulario muestra el **límite real del servidor** («hasta 2 MB cada
  uno»), una **barra de progreso**, el contador «3 de 4 archivos» y un botón
  **✕ para quitar** cualquier archivo antes de publicar.
- Si un archivo no cabe, se avisa **por su nombre** y con la causa, en vez de
  fallar en silencio.
- La pestaña Comunidad del panel enseña los valores de `upload_max_filesize` y
  `post_max_size` detectados en el servidor.

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
  admita 465 KB y que el usuario web pueda escribir en `themes/` y `public/`).
- **Si el subidor rechaza archivos grandes:** sube `cyberpunk-extension-con-tema.zip`
  (239 KB) por el panel y copia `cyberpunk-assets.zip` a `public/cyberpunk/` por FTP.

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
